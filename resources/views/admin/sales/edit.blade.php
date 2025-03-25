<x-authenticated-layout>
    <x-slot name="head">
        <title>Sale | Update</title>
    </x-slot>

    <section class="Sales">
        <div class="custom_form">
            <div class="header">
                <div class="icon">
                    <a href="{{ route('sales.index') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </div>
                <p>Update Sale</p>
            </div>

            <form action="{{ route('sales.update', $sale->id) }}" method="post">
                @csrf
                @method('patch')

                <div class="order_details row_container">
                    <div class="details">
                        <p class="text-success">
                            <span>Order_number</span>
                            <span>{{ $sale->order_number ?? '-' }}</span>
                        </p>
                        <p>
                            <span>Order Date</span>
                            <span>{{ $sale->created_at?->format('d M Y \a\t h:i A') ?? '-' }}</span>
                        </p>
                    </div>
    
                    <div class="cart_items">
                        <p class="bold">Items Ordered</p>
    
                        <ol>
                            @foreach($sale->items as $product)
                            <li>
                                <span>{{ $product['title'] }} : </span>
                                <span>{{ $product['quantity'] }} @ {{ $product['selling_price'] }}</span>
                                <span>= Ksh. {{ number_format($product['selling_price'] * $product['quantity'], 2) }}</span>
                            </li>
                            @endforeach
                        </ol>
                        <p class="text-success bold">
                            <span>Total Amount : </span>
                            <span>Ksh. {{ number_format($sale->total_amount, 2) }}</span>
                        </p>
    
                        <div class="payment_details">
                            <p>
                                <span>Payment : </span>
                                <span>
                                    @if ($amount_paid_display == 'Paid')
                                        <i class="fa fa-check-circle title success"></i>
                                    @elseif ($amount_paid_display == 'Underpaid')
                                        <span class="danger title">Ksh. {{ number_format($sale->amount_paid ?? 0, 2) }}</span>
                                    @elseif ($amount_paid_display == 'Overpaid')
                                        <span class="success title">Ksh. {{ number_format($sale->amount_paid ?? 0, 2) }}</span>
                                    @else
                                        <span>{{ $sale->amount_paid ?? '-' }}</span>
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="payment_method">Payment Method</label>
                        <div class="custom_radio_buttons">
                            @foreach(App\Models\Sales\Sale::PAYMENTMETHODS as $method)
                                <label>
                                    <input class="option_radio" 
                                        type="radio" 
                                        name="payment_method" 
                                        value="{{ $method }}"
                                        {{ old('payment_method', $sale->payment_method) == $method ? 'checked' : '' }}>
                                    <span>{{ ucfirst($method) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error field="payment_method" />
                    </div>

                    <div class="inputs">
                        <label for="amount_paid">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $sale->amount_paid) }}">
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit">Update Sale</button>

                    <button type="button" class="btn_success" onclick="printReceipt()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>

                    @can('view-as-super-admin')
                        <button type="button" class="btn_danger" onclick="deleteItem({{ $sale->id }}, 'sale');"
                            form="deleteForm_{{ $sale->id }}">
                            <i class="fas fa-trash-alt delete"></i>
                            <span>Delete Sale</span>                        
                        </button>
                    @endcan
                </div>
            </form>

            @can('view-as-super-admin')
                <form id="deleteForm_{{ $sale->id }}" action="{{ route('sales.destroy', $sale->id) }}" method="post"
                    style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
        </div>

        <div id="receipt" style="display: none;">
            <div class="receipt-content">
                <h2>Receipt</h2>
                <p><strong>Order Number:</strong> {{ $sale->order_number ?? '-' }}</p>
                <p><strong>Order Date:</strong> {{ $sale->created_at?->format('d M Y') ?? '-' }}</p>

                <h3>Items Ordered:</h3>
                <ul>
                    @foreach($sale->items as $product)
                    <li>
                        {{ $product['name'] }} - {{ $product['quantity'] }} @ Ksh. {{ number_format($product['selling_price'], 2) }} 
                        = Ksh. {{ number_format($product['selling_price'] * $product['quantity'], 2) }}
                    </li>
                    @endforeach
                </ul>

                <p class="bold"><strong>Total Amount:</strong> Ksh. {{ number_format($sale->total_amount, 2) }}</p>
                <p><strong>Amount Paid:</strong> Ksh. {{ number_format($sale->amount_paid ?? 0, 2) }}</p>

                <h3>Thank You for Shopping with Us!</h3>
            </div>
        </div>
    </section>

    <x-slot name="scripts">
        <x-sweetalert />
        <script>
            function printReceipt() {
                var receiptContent = document.getElementById("receipt").innerHTML;
                var printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print Receipt</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                text-align: center; 
                            }
                            
                            .receipt-content { 
                                width: 80%; 
                                margin: auto; 
                                padding: 20px; 
                                border: 1px solid #ccc; 
                            }
                            
                            h2, h3 { 
                                color: #333; 
                            }
                            
                            p, li { 
                                font-size: 16px; 
                            }
                        </style>
                    </head>
                    <body onload="window.print(); window.close();">
                        ${receiptContent}
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }
        </script>
    </x-slot>
</x-authenticated-layout>
