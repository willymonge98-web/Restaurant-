<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CashSession;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RestaurantSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderPaymentService
{
    public function __construct(private StockService $stockService) {}

    /**
     * @param  array<int, array{method:string,amount:float|string,currency?:string,reference?:string}>  $payments
     */
    public function pay(Order $order, array $payments, float $discountAmount = 0): Order
    {
        if ($order->status === Order::STATUS_ANNULEE) {
            throw new RuntimeException('Commande annulée.');
        }

        if ($order->status === Order::STATUS_PAYEE) {
            throw new RuntimeException('Commande déjà payée.');
        }

        return DB::transaction(function () use ($order, $payments, $discountAmount) {
            $settings = RestaurantSetting::current();
            $order->discount_amount = max(0, $discountAmount);
            $order->recalculateTotals((float) $settings->tax_rate);
            $order->refresh();

            $session = Auth::user()?->openCashSession();
            $totalPaidNow = 0.0;

            foreach ($payments as $row) {
                $amount = round((float) $row['amount'], 2);
                if ($amount <= 0) {
                    continue;
                }

                $method = $row['method'];
                if (! array_key_exists($method, Payment::METHODS)) {
                    throw new RuntimeException("Méthode de paiement invalide: {$method}");
                }

                $currency = $row['currency'] ?? $settings->currency_primary;
                $amountUsd = $amount;
                if (strtoupper($currency) === strtoupper($settings->currency_secondary)) {
                    $rate = max(0.0001, (float) $settings->exchange_rate);
                    $amountUsd = round($amount / $rate, 2);
                }

                Payment::query()->create([
                    'order_id' => $order->id,
                    'cash_session_id' => $session?->id,
                    'user_id' => Auth::id(),
                    'method' => $method,
                    'currency' => $currency,
                    'amount' => $amount,
                    'amount_usd' => $amountUsd,
                    'reference' => $row['reference'] ?? null,
                    'status' => 'completed',
                    'meta' => [
                        'operator' => in_array($method, ['mpesa', 'orange_money', 'airtel_money'], true) ? $method : null,
                        'note' => 'Enregistrement local — API opérateur à brancher (Phase 2+)',
                    ],
                ]);

                $totalPaidNow += $amountUsd;
            }

            $order->paid_amount = round((float) $order->paid_amount + $totalPaidNow, 2);
            $order->cashier_id = Auth::id();

            if ($order->paid_amount + 0.009 >= (float) $order->total) {
                $this->stockService->deductForOrder($order);
                $order->status = Order::STATUS_PAYEE;
                $order->validated_at = now();
                $order->paid_amount = (float) $order->total;

                if ($order->dining_table_id) {
                    DiningTable::query()->whereKey($order->dining_table_id)->update([
                        'status' => DiningTable::STATUS_NETTOYAGE,
                    ]);
                }
            }

            $order->save();

            ActivityLog::record('order.payment', $order, 'Paiement enregistré pour '.$order->number, [
                'paid_now' => $totalPaidNow,
                'paid_total' => $order->paid_amount,
            ]);

            return $order->fresh(['items', 'payments', 'diningTable']);
        });
    }

    public function cancel(Order $order, string $reason): Order
    {
        if ($order->status === Order::STATUS_ANNULEE) {
            throw new RuntimeException('Déjà annulée.');
        }

        return DB::transaction(function () use ($order, $reason) {
            if ($order->stock_deducted) {
                $this->stockService->restoreForOrder($order);
            }

            $order->forceFill([
                'status' => Order::STATUS_ANNULEE,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ])->save();

            if ($order->dining_table_id) {
                DiningTable::query()->whereKey($order->dining_table_id)->update([
                    'status' => DiningTable::STATUS_LIBRE,
                ]);
            }

            ActivityLog::record('order.cancel', $order, 'Annulation '.$order->number.': '.$reason);

            return $order->fresh();
        });
    }

    public function closeCashSession(CashSession $session, float $counted, ?string $notes = null): CashSession
    {
        return DB::transaction(function () use ($session, $counted, $notes) {
            $payments = $session->payments()->where('status', 'completed')->get();
            $byMethod = [];
            $cashUsd = 0.0;
            $totalSales = 0.0;

            foreach ($payments as $payment) {
                $usd = (float) ($payment->amount_usd ?? $payment->amount);
                $totalSales += $usd;
                $byMethod[$payment->method] = ($byMethod[$payment->method] ?? 0) + $usd;
                if ($payment->method === 'cash') {
                    $cashUsd += $usd;
                }
            }

            $theoretical = (float) $session->opening_float + $cashUsd;

            $session->forceFill([
                'status' => 'closed',
                'closing_counted' => $counted,
                'theoretical_cash' => round($theoretical, 2),
                'difference' => round($counted - $theoretical, 2),
                'total_sales' => round($totalSales, 2),
                'totals_by_method' => $byMethod,
                'notes' => $notes,
                'closed_at' => now(),
            ])->save();

            ActivityLog::record('cash.close', $session, 'Clôture de caisse #'.$session->id);

            return $session->fresh();
        });
    }
}
