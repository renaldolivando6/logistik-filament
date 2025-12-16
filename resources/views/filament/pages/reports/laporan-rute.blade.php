<x-filament-panels::page>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Total Rute Aktif
            </div>
            <div class="mt-2 text-3xl font-bold">
                {{ number_format($this->getSummaryData()['total_routes']) }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Rute Terprofitabel
            </div>
            <div class="mt-2 text-xl font-bold text-green-600 dark:text-green-400">
                {{ $this->getSummaryData()['most_profitable'] }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Rute Tersering
            </div>
            <div class="mt-2 text-xl font-bold text-blue-600 dark:text-blue-400">
                {{ $this->getSummaryData()['most_frequent'] }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Volume Tertinggi
            </div>
            <div class="mt-2 text-xl font-bold text-purple-600 dark:text-purple-400">
                {{ $this->getSummaryData()['highest_volume'] }}
            </div>
        </x-filament::card>
    </div>
    
    {{-- Chart Section --}}
    <x-filament::card class="mb-6">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-4 dark:text-white">Top 10 Rute by Revenue</h3>
            <div style="height: 400px;">
                <canvas id="routeChart"></canvas>
            </div>
        </div>
    </x-filament::card>
    
    {{-- Table Section --}}
    <x-filament::card>
        {{ $this->table }}
    </x-filament::card>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('routeChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Waru → Gresik', 'Surabaya → Malang', 'Gresik → Jombang', 'Tuban → Surabaya', 'Pasuruan → Bangkalan'],
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: [85000000, 72000000, 65000000, 58000000, 45000000],
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>