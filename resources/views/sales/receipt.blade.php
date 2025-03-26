<x-authenticated-layout>
    <x-slot name="head">
        <title>Receipt</title>
        <style>
            @media print {
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    width: 58mm; /* Adjust for 58mm or 80mm */
                }

                .receipt-container {
                    width: 100%;
                    max-width: 58mm; /* Ensure the receipt width is within the thermal paper */
                    padding: 5px;
                    margin: 0;
                }

                h2 {
                    text-align: center;
                    font-size: 14px;
                    margin-bottom: 5px;
                }

                p {
                    margin: 2px 0;
                    text-align: center;
                }

                .receipt-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10px;
                }

                .receipt-table th,
                .receipt-table td {
                    border-bottom: 1px dashed #000;
                    padding: 2px;
                    text-align: left;
                }

                .receipt-table th {
                    font-size: 12px;
                    text-align: left;
                }

                .receipt-table td {
                    font-size: 10px;
                }

                .print-button {
                    display: none; /* Hide the print button when printing */
                }
            }
        </style>
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
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->selling_price, 2) }}</td>
                            <td>{{ number_format($item->selling_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Total: Ksh. {{ number_format($sale->total_amount, 2) }}</strong></p>
            <p>Amount Paid: Ksh. {{ number_format($sale->amount_paid, 2) }}</p>
            <p>Change Given: Ksh. {{ number_format($sale->amount_paid - $sale->total_amount, 2) }}</p>
            <p><b>Thank you and welcome again!</b></p>

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
