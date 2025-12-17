<x-filament-panels::page>
    <x-filament::card class="mb-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Laporan</h3>
                @if($hasAppliedFilter)
                    <x-filament::badge color="success" icon="heroicon-o-funnel">Filter Aktif</x-filament::badge>
                @endif
            </div>
            
            <form wire:submit="applyFilters">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dari Tanggal</label>
                        <input type="date" wire:model="dari_tanggal" class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sampai Tanggal</label>
                        <input type="date" wire:model="sampai_tanggal" class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kendaraan</label>
                        <select wire:model="kendaraan_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Kendaraan</option>
                            @foreach($this->getKendaraanOptions() as $id => $nopol)
                                <option value="{{ $id }}">{{ $nopol }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sopir</label>
                        <select wire:model="sopir_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Sopir</option>
                            @foreach($this->getSopirOptions() as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Catatan:</strong> Laporan ini hanya menampilkan trip dengan status <span class="font-semibold">"Selesai"</span>. Trip dengan status Draft, Berangkat, atau Batal tidak akan dihitung dalam profitabilitas.
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <x-filament::button type="submit" color="primary" icon="heroicon-o-funnel" size="lg">Tampilkan Laporan</x-filament::button>
                    @if($hasAppliedFilter)
                        <x-filament::button type="button" color="gray" icon="heroicon-o-x-mark" wire:click="resetFilters" size="lg">Reset Filter</x-filament::button>
                    @endif
                </div>
            </form>
        </div>
    </x-filament::card>
    
    @if($hasAppliedFilter)
        <x-filament::card class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-200">Filter Aktif</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                            @if($dari_tanggal && $sampai_tanggal)
                                Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}
                            @elseif($dari_tanggal)
                                Dari: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }}
                            @elseif($sampai_tanggal)
                                Sampai: {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}
                            @else
                                Semua Periode
                            @endif
                            @if($kendaraan_id) • Kendaraan: {{ \App\Models\Kendaraan::find($kendaraan_id)?->nopol }} @endif
                            @if($sopir_id) • Sopir: {{ \App\Models\Sopir::find($sopir_id)?->nama }} @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <x-filament::button color="success" icon="heroicon-o-arrow-down-tray" wire:click="exportToExcel" size="sm">Export Excel</x-filament::button>
                    <x-filament::button color="danger" icon="heroicon-o-document-text" wire:click="exportToPdf" size="sm">Export PDF</x-filament::button>
                </div>
            </div>
        </x-filament::card>
        
        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-3">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Trip</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->getSummaryData()['total_trips']) }}</div>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-truck class="w-8 h-8 text-blue-600 dark:text-blue-400"/>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</div>
                        <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($this->getSummaryData()['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <x-heroicon-o-banknotes class="w-8 h-8 text-green-600 dark:text-green-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Biaya</div>
                        <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($this->getSummaryData()['total_costs'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-heroicon-o-receipt-percent class="w-8 h-8 text-red-600 dark:text-red-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Profit</div>
                        <div class="mt-2 text-2xl font-bold {{ $this->getSummaryData()['total_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">Rp {{ number_format($this->getSummaryData()['total_profit'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 {{ $this->getSummaryData()['total_profit'] >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full">
                        <x-heroicon-o-currency-dollar class="w-8 h-8 {{ $this->getSummaryData()['total_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit Margin</div>
                        <div class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->getSummaryData()['profit_margin'], 1) }}%</div>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-chart-pie class="w-8 h-8 text-blue-600 dark:text-blue-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Profit/Trip</div>
                        <div class="mt-2 text-xl font-bold text-purple-600 dark:text-purple-400">Rp {{ number_format($this->getSummaryData()['avg_profit_per_trip'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <x-heroicon-o-calculator class="w-8 h-8 text-purple-600 dark:text-purple-400"/>
                    </div>
                </div>
            </x-filament::card>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Profit 6 Bulan</h3>
                    <div class="relative h-[350px]">
                        <canvas id="monthlyProfitChart" data-chart-data="{{ json_encode($this->getMonthlyProfitData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Sopir Paling Profitable</h3>
                    <div class="relative h-[350px]">
                        <canvas id="topDriversChart" data-chart-data="{{ json_encode($this->getTopDriversData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Breakdown Biaya Operasional</h3>
                    <div class="relative h-[350px]">
                        <canvas id="costBreakdownChart" data-chart-data="{{ json_encode($this->getCostBreakdownData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Kendaraan Paling Profitable</h3>
                    <div class="relative h-[350px]">
                        <canvas id="topVehiclesChart" data-chart-data="{{ json_encode($this->getTopVehiclesData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
        </div>
        
        <x-filament::card>
            <div class="overflow-x-auto">
                {{ $this->table }}
            </div>
            
            @if($hasAppliedFilter)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</div>
                            <div class="mt-1 text-xl font-bold text-green-600 dark:text-green-400">
                                Rp {{ number_format($this->getSummaryData()['total_revenue'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Biaya</div>
                            <div class="mt-1 text-xl font-bold text-red-600 dark:text-red-400">
                                Rp {{ number_format($this->getSummaryData()['total_costs'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-center p-4 {{ $this->getSummaryData()['total_profit'] >= 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-lg">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Profit</div>
                            <div class="mt-1 text-2xl font-bold {{ $this->getSummaryData()['total_profit'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($this->getSummaryData()['total_profit'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::card>

    @else
        <x-filament::card>
            <div class="flex flex-col items-center justify-center py-16">
                <div class="w-24 h-24 mb-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                    <x-heroicon-o-funnel class="w-12 h-12 text-gray-400 dark:text-gray-600"/>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Belum Ada Filter</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Silakan pilih filter dan klik "Tampilkan Laporan".</p>
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>

@script
<script>
    window.initProfitCharts = () => {
        if (typeof Chart === 'undefined') {
            setTimeout(window.initProfitCharts, 500);
            return;
        }

        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(val);
        };

        // Monthly Profit Trend
        const monthCtx = document.getElementById('monthlyProfitChart');
        if (monthCtx) {
            if (window.monthProfitChart) window.monthProfitChart.destroy();
            const dataRaw = monthCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], revenue: [], costs: [], profit: [] };
            
            if (data.labels && data.labels.length > 0) {
                window.monthProfitChart = new Chart(monthCtx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Revenue',
                                data: data.revenue,
                                borderColor: 'rgba(16, 185, 129, 1)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 4
                            },
                            {
                                label: 'Biaya',
                                data: data.costs,
                                borderColor: 'rgba(239, 68, 68, 1)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 4
                            },
                            {
                                label: 'Profit',
                                data: data.profit,
                                borderColor: 'rgba(59, 130, 246, 1)',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': ' + formatRupiah(ctx.raw)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (val) => 'Rp ' + (val / 1000000).toFixed(0) + 'jt' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }

        // Top Drivers
        const driverCtx = document.getElementById('topDriversChart');
        if (driverCtx) {
            if (window.driverChart) window.driverChart.destroy();
            const dataRaw = driverCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], profit: [], trips: [] };
            
            if (data.labels && data.labels.length > 0) {
                window.driverChart = new Chart(driverCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Profit',
                                data: data.profit,
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Total Trip',
                                data: data.trips,
                                type: 'line',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                pointRadius: 4,
                                tension: 0.3,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const lbl = ctx.dataset.label || '';
                                        const val = ctx.dataset.yAxisID === 'y' ? formatRupiah(ctx.raw) : ctx.raw + ' trip';
                                        return lbl + ': ' + val;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                type: 'linear', position: 'left', beginAtZero: true,
                                ticks: { callback: (val) => 'Rp ' + (val / 1000000).toFixed(0) + 'jt' }
                            },
                            y1: { 
                                type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            }
        }

        // Cost Breakdown (Pie Chart)
        const costCtx = document.getElementById('costBreakdownChart');
        if (costCtx) {
            if (window.costChart) window.costChart.destroy();
            const dataRaw = costCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], data: [] };
            
            if (data.labels && data.labels.length > 0) {
                const colors = [
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(236, 72, 153, 0.7)',
                ];
                
                window.costChart = new Chart(costCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: colors,
                            borderColor: colors.map(c => c.replace('0.7', '1')),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.label + ': ' + formatRupiah(ctx.raw)
                                }
                            }
                        }
                    }
                });
            }
        }

        // Top Vehicles
        const vehicleCtx = document.getElementById('topVehiclesChart');
        if (vehicleCtx) {
            if (window.vehicleChart) window.vehicleChart.destroy();
            const dataRaw = vehicleCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], profit: [] };
            
            if (data.labels && data.labels.length > 0) {
                window.vehicleChart = new Chart(vehicleCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Profit',
                            data: data.profit,
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderColor: 'rgba(139, 92, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => 'Profit: ' + formatRupiah(ctx.raw)
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { callback: (val) => 'Rp ' + (val / 1000000).toFixed(0) + 'jt' }
                            },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    };

    window.initProfitCharts();
    document.addEventListener('livewire:navigated', () => setTimeout(window.initProfitCharts, 200));
    window.addEventListener('initCharts', () => setTimeout(window.initProfitCharts, 200));
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => setTimeout(window.initProfitCharts, 300));
    });
</script>
@endscript