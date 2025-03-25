<x-authenticated-layout>
    <x-slot name="head">
        <title>Receipt</title>
    </x-slot>

    <section class="receipt">
        <div class="receipt-container">
            <h2>Receipt</h2>
            <p>Order Number: {{ $sale->order_number }}</p>
            <p>Date: {{ $sale->created_at->format('d-m-Y H:i') }}</p>
            <p>Payment Method: {{ ucfirst($sale->payment_method) }}</p>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Ksh. {{ number_format($item->selling_price, 2) }}</td>
                            <td>Ksh. {{ number_format($item->selling_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Total: Ksh. {{ number_format($sale->total_amount, 2) }}</strong></p>
            <p>Amount Paid: Ksh. {{ number_format($sale->amount_paid, 2) }}</p>
            <p>Change Given: Ksh. {{ number_format($sale->amount_paid - $sale->total_amount, 2) }}</p>

            <button onclick="window.print()" class="print-button">Print Receipt</button>
        </div>
    </section>

    <x-slot name="scripts">
        <script>
            document.querySelector('.print-button').addEventListener('click', function () {
                window.print();
            });
        </script>
    </x-slot>
</x-authenticated-layout>
