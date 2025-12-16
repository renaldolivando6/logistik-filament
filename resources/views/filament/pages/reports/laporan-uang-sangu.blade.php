<x-filament-panels::page>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-5">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Total Uang Sangu
            </div>
            <div class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">
                Rp {{ number_format($this->getSummaryData()['total_sangu'], 0, ',', '.') }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Total Biaya
            </div>
            <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">
                Rp {{ number_format($this->getSummaryData()['total_expenses'], 0, ',', '.') }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Dikembalikan
            </div>
            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                Rp {{ number_format($this->getSummaryData()['total_returned'], 0, ',', '.') }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Outstanding
            </div>
            <div class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400">
                Rp {{ number_format($this->getSummaryData()['outstanding'], 0, ',', '.') }}
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Lewat Waktu
            </div>
            <div class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
                {{ number_format($this->getSummaryData()['overdue_count']) }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                trip > 7 hari
            </div>
        </x-filament::card>
    </div>
    
    {{-- Alert for Overdue --}}
    @if($this->getSummaryData()['overdue_count'] > 0)
    <x-filament::card class="mb-6 border-l-4 border-red-500">
        <div class="flex items-center p-4">
            <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h3 class="font-semibold text-red-600 dark:text-red-400">Perhatian!</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Ada {{ $this->getSummaryData()['overdue_count'] }} trip dengan uang sangu yang belum diselesaikan lebih dari 7 hari.
                </p>
            </div>
        </div>
    </x-filament::card>
    @endif
    
    {{-- Chart Section --}}
    <x-filament::card class="mb-6">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-4 dark:text-white">Status Pengembalian Uang Sangu</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div style="height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
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
            // Status Chart (Pie)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Belum Selesai', 'Lewat Waktu'],
                    datasets: [{
                        data: [65, 25, 10],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(251, 146, 60)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Trend Chart (Line)
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Uang Sangu',
                        data: [8000000, 12000000, 9500000, 11000000],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Dikembalikan',
                        data: [7200000, 10800000, 8500000, 9900000],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
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