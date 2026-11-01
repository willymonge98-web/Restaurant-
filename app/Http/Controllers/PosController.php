<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\RestaurantSetting;
use App\Services\OrderPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $openOrders = Order::with('diningTable')
            ->whereNotIn('status', [Order::STATUS_PAYEE, Order::STATUS_ANNULEE])
            ->latest()
            ->get();

        return view('pos.index', [
            'orders' => $openOrders,
            'session' => auth()->user()->openCashSession(),
        ]);
    }

    public function checkout(Order $order): View
    {
        $order->load(['items', 'payments', 'diningTable']);

        return view('pos.checkout', [
            'order' => $order,
            'methods' => Payment::METHODS,
            'settings' => RestaurantSetting::current(),
            'session' => auth()->user()->openCashSession(),
        ]);
    }

    public function pay(Request $request, Order $order, OrderPaymentService $service): RedirectResponse
    {
        $data = $request->validate([
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'in:cash,card,mpesa,orange_money,airtel_money'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.currency' => ['nullable', 'string', 'max:10'],
            'payments.*.reference' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $service->pay($order, $data['payments'], (float) ($data['discount_amount'] ?? 0));
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => $e->getMessage()])->withInput();
        }

        return redirect()->route('pos.receipt', $order)->with('success', 'Paiement enregistré.');
    }

    public function receipt(Order $order): View
    {
        $order->load(['items', 'payments', 'diningTable', 'cashier']);

        return view('pos.receipt', [
            'order' => $order,
            'settings' => RestaurantSetting::current(),
        ]);
    }

    public function receiptPdf(Order $order): Response
    {
        $order->load(['items', 'payments', 'diningTable', 'cashier']);
        $settings = RestaurantSetting::current();

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'settings'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // ~80mm thermal stub

        return $pdf->download('ticket-'.$order->number.'.pdf');
    }
}
