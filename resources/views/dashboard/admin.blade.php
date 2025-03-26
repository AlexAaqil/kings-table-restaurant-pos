<section class="AdminDashboard">
    <div class="section stats">
        <div class="stat">
            <div class="text">
                <span>{{ $count_admins }}</span>
                <span>Admins & {{ $count_cashiers }} Cashiers</span>
            </div>

            <div class="details"></div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_products }}</span>
                <span>Products & {{ $count_product_categories }} Categories</span>
            </div>

            <div class="details"></div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_total_sales }}</span>
                <span>Total Sales</span>
            </div>

            <div class="details"></div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_sales_this_month }}</span>
                <span>Sales this month</span>
            </div>

            <div class="details">
                <span class="{{ $change_this_month >= 0 ? 'success' : 'danger' }}">
                    {{ $change_this_month >= 0 ? '+' : '' }}{{ $change_this_month }} %
                </span>
            </div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_sales_this_week }}</span>
                <span>Sales this week</span>
            </div>

            <div class="details">
                <span class="{{ $change_this_week >= 0 ? 'success' : 'danger' }}">
                    {{ $change_this_week >= 0 ? '+' : '' }}{{ $change_this_week }} %
                </span>
            </div>
        </div>

        <div class="stat">
            <div class="text">
                <span>{{ $count_sales_today }}</span>
                <span>Sales today</span>
            </div>

            <div class="details">
                <span class="{{ $change_today >= 0 ? 'success' : 'danger' }}">
                    {{ $change_today >= 0 ? '+' : '' }}{{ $change_today }} %
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
                        <span>: {{ number_format($sales_this_month, 2) }}</span>
                    </p>
                    <p>
                        <span>Last Week</span>
                        <span>: {{ number_format($sales_last_week, 2) }}</span>
                    </p>
                    <p>
                        <span>This Week</span>
                        <span>: {{ number_format($sales_this_week, 2) }}</span>
                    </p>
                    <p>
                        <span>Yesterday</span>
                        <span>: {{ number_format($sales_yesterday, 2) }}</span>
                    </p>
                    <p>
                        <span>Today</span>
                        <span>: {{ number_format($sales_today, 2) }}</span>
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
                        data: {!! json_encode($sales_data) !!},
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
