<x-authenticated-layout>
    <x-slot name="head">
        <title>Sales</title>
    </x-slot>

    <section class="Sales">
        <div class="body">
            @if ($sales->isNotEmpty())
                <div class="table">
                    <div class="header">
                        <div class="details">
                            <p class="title">Sales</p>
                            <p class="stats">
                                <span>{{ $count_sales }} {{ Str::plural('Sale', $count_sales) }}</span>
                                <span>{{ $count_sales_today }} Today</span>
                            </p>
                        </div>
    
                        <x-search-input />
                    </div>
    
                    <table>
                        <thead>
                            <tr>
                                <th class="center">#</th>
                                <th>Order</th>
                                <th>Amount</th>
                                <th class="center">Payment</th>
                            </tr>
                        </thead>
            
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="searchable">
                                    <td class="center">{{ $loop->iteration }}</td>
                                    <td>{{ $sale->order_number }}</td>
                                    <td>{{ $sale->total_amount ?? '-' }}</td>
                                    <td class="center">
                                        @if($sale->amount_paid >= $sale->total_amount)
                                            <i class="fa fa-check-circle success"></i>
                                        @else
                                            <i class="fa fa-times-circle danger"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No users yet.</p>
            @endif
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
