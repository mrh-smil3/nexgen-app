<div>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Package Sales Dashboard</h1>
        
        <div class="mb-4">
            {{ $this->form }}
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-4">Penjualan Paket</h2>
                <div wire:ignore>
                    <canvas id="packageSalesChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            var ctx = document.getElementById('packageSalesChart').getContext('2d');
            var packageSalesData = @json($packageSalesData);

            var labels = packageSalesData.map(item => item.name);
            var data = packageSalesData.map(item => item.total_sales);

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Penjualan'
                            }
                        }
                    }
                }
            });

            // Livewire event listener untuk update chart
            Livewire.on('updateChart', (packageSalesData) => {
                chart.data.labels = packageSalesData.map(item => item.name);
                chart.data.datasets[0].data = packageSalesData.map(item => item.total_sales);
                chart.update();
            });
        });
    </script>
    @endpush
</div>