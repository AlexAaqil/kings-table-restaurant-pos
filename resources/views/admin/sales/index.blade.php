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
                            </p>
                        </div>
    
                        <x-search-input />

                        <form method="GET" action="{{ route('sales.index') }}" class="filter-form">
                            <div class="filters">
                                <div class="filter">
                                    <label for="cashier">Cashier:</label>
                                    <select name="cashier" id="cashier">
                                        <option value="">All</option>
                                        @foreach($cashiers as $cashier)
                                            <option value="{{ $cashier->id }}" {{ request('cashier') == $cashier->id ? 'selected' : '' }}>
                                                {{ $cashier->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter">
                                    <label for="date">Date:</label>
                                    <input type="date" name="date" id="date" value="{{ request('date') }}">
                                </div>

                                <div class="filter">
                                    <label for="time_start">Start Time:</label>
                                    <input type="time" name="time_start" id="time_start" value="{{ request('time_start') }}">
                                </div>

                                <div class="filter">
                                    <label for="time_end">End Time:</label>
                                    <input type="time" name="time_end" id="time_end" value="{{ request('time_end') }}">
                                </div>

                                <div class="filter">
                                    <button type="submit" class="btn">Filter</button>
                                    <a href="{{ route('sales.index') }}" class="btn reset">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
    
                    <table>
                        <thead>
                            <tr>
                                <th class="center">#</th>
                                <th>Order</th>
                                <th>Amount</th>
                                <th class="center">Payment</th>
                                <th class="actions center">Actions</th>
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
                                    <td class="actions center">
                                        <div class="action">
                                            <a href="{{ route('sales.edit', $sale->id) }}">
                                                <span class="fas fa-eye"></span> 
                                            </a> 
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No sales yet.</p>
            @endif
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
