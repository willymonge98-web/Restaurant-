<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; text-align: center; }
        .muted { color: #555; text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; }
        .right { text-align: right; }
        .total { font-weight: bold; border-top: 1px solid #000; padding-top: 6px; }
        .footer { margin-top: 14px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ $settings->name }}</h1>
    <div class="muted">{{ $settings->address }}<br>{{ $settings->phone }} · {{ $settings->email }}</div>
    <div>{{ $order->number }}<br>{{ optional($order->validated_at)->format('d/m/Y H:i') }}</div>
    <hr>
    <table>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->quantity }}× {{ $item->product_name }}</td>
                <td class="right">${{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
        <tr><td>Remise</td><td class="right">-${{ number_format($order->discount_amount, 2) }}</td></tr>
        <tr><td>Taxe</td><td class="right">${{ number_format($order->tax_amount, 2) }}</td></tr>
        <tr class="total"><td>TOTAL</td><td class="right">${{ number_format($order->total, 2) }}</td></tr>
        @foreach ($order->payments as $payment)
            <tr>
                <td>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</td>
                <td class="right">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
            </tr>
        @endforeach
    </table>
    <div class="footer">{{ $settings->ticket_footer }}</div>
</body>
</html>
