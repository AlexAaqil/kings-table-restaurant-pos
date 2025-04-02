<section class="UserDashboard">
    <div class="section hero">
        <p class="title">Hi, {{ Auth::user()->full_name }}</p>
         @if ($active_shift)
            <p>Shift started at: {{ $active_shift->shift_start->format('d-m-Y h:i A') }}</p>
            <form action="{{ route('work-shift.end') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-danger">End Shift</button>
            </form>
        @else
            <form action="{{ route('work-shift.start') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-success">Start Shift</button>
            </form>
        @endif
    </div>

    <div class="section stats">
        <div class="stat">
            <div class="text">
                <span>{{ $count_cashier_total_sales }}</span>
                <span>Total Sales</span>
            </div>

            <div class="details"></div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_cashier_sales_this_month }}</span>
                <span>Sales this month</span>
            </div>

            <div class="details">
                <span class="{{ $cashier_change_this_month >= 0 ? 'success' : 'danger' }}">
                    {{ $cashier_change_this_month >= 0 ? '+' : '' }}{{ $cashier_change_this_month }} %
                </span>
            </div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_cashier_sales_this_week }}</span>
                <span>Sales this week</span>
            </div>

            <div class="details">
                <span class="{{ $change_this_week >= 0 ? 'success' : 'danger' }}">
                    {{ $cashier_change_this_week >= 0 ? '+' : '' }}{{ $cashier_change_this_week }} %
                </span>
            </div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_cashier_sales_today }}</span>
                <span>Sales today</span>
            </div>

            <div class="details">
                <span class="{{ $change_today >= 0 ? 'success' : 'danger' }}">
                    {{ $cashier_change_today >= 0 ? '+' : '' }}{{ $cashier_change_today }} %
                </span>
            </div>
        </div>
    </div>

    <div class="section Charts">
        <div class="charts">
            <div class="chart">
                <h2>Sales</h2>
                <canvas id="salesChart"></canvas>
            </div>

            <div class="chart">
                <h2>Summary</h2>
                <div class="sales_summary">
                    <p>
                        <span>This Month</span>
                        <span>: {{ number_format($cashier_sales_this_month, 2) }}</span>
                    </p>
                    <p>
                        <span>Last Week</span>
                        <span>: {{ number_format($cashier_sales_last_week, 2) }}</span>
                    </p>
                    <p>
                        <span>This Week</span>
                        <span>: {{ number_format($cashier_sales_this_week, 2) }}</span>
                    </p>
                    <p>
                        <span>Yesterday</span>
                        <span>: {{ number_format($cashier_sales_yesterday, 2) }}</span>
                    </p>
                    <p>
                        <span>Today</span>
                        <span>: {{ number_format($cashier_sales_today, 2) }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script>
            const ctx = document.getElementById('salesChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Amount',
                        data: {!! json_encode($cashier_sales_data) !!},
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        </script>
    </x-slot>
</section>
