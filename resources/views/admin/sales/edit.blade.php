<x-authenticated-layout>
    <x-slot name="head">
        <title>Sale | Update</title>

        <style>
            .hidden {
                display: none;
            }
        </style>
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
                            <span>Order_number: </span>
                            <span>{{ $sale->sale_reference ?? '-' }}</span>
                        </p>
                        <p>
                            <span>Date: </span>
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
                            @foreach(App\Models\Sales\SalePayment::PAYMENTMETHODS as $method)
                                <label>
                                    <input class="option_radio payment-method" 
                                        type="radio" 
                                        name="payment_method" 
                                        value="{{ $method }}"
                                        {{ old('payment_method', $sale->sale_payment->payment_method ?? '') == $method ? 'checked' : '' }}>
                                    <span>{{ ucfirst($method) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error field="payment_method" />
                    </div>

                    <div id="electronic-payment-fields" class="hidden">
                        <div class="inputs">
                            <label for="transaction_reference">Transaction Reference</label>
                            <input type="text" name="transaction_reference" id="transaction_reference" 
                                value="{{ old('transaction_reference', $sale->sale_payment->transaction_reference ?? '') }}">
                        </div>

                        <div class="inputs">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" 
                                value="{{ old('customer_name', $sale->sale_payment->customer_name ?? '') }}">
                        </div>
                    </div>

                    <div class="inputs">
                        <label for="amount_paid">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $sale->amount_paid) }}">
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit">Update Sale</button>

                    <a href="{{ route('sales.receipt', $sale) }}" class="btn_link">Print Receipt</a>

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
    </section>

    <x-slot name="scripts">
        <x-sweetalert />

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let paymentMethods = document.querySelectorAll('.payment-method');
                let electronicFields = document.getElementById('electronic-payment-fields');

                function toggleFields() {
                    let selectedMethod = document.querySelector('.payment-method:checked')?.value;
                    if (selectedMethod && selectedMethod !== 'cash') {
                        electronicFields.classList.remove('hidden');
                    } else {
                        electronicFields.classList.add('hidden');
                    }
                }

                paymentMethods.forEach(method => {
                    method.addEventListener('change', toggleFields);
                });

                // Run on page load to set correct visibility
                toggleFields();
            });
        </script>
    </x-slot>
</x-authenticated-layout>
