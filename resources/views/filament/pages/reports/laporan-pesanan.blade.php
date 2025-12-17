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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pelanggan</label>
                        <select wire:model="pelanggan_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Pelanggan</option>
                            @foreach($this->getPelangganOptions() as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item Muatan</label>
                        <select wire:model="item_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Item</option>
                            @foreach($this->getItemOptions() as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rute</label>
                        <select wire:model="rute_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Rute</option>
                            @foreach($this->getRuteOptions() as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status Pesanan</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($this->getStatusOptions() as $key => $label)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-all hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 {{ in_array($key, $status) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input type="checkbox" wire:model="status" value="{{ $key }}" class="fi-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" wire:click="$set('status', ['draft', 'dalam_perjalanan', 'selesai', 'batal'])" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">✓ Pilih Semua</button>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <button type="button" wire:click="$set('status', [])" class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 font-medium">✕ Hapus Semua</button>
                        </div>
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
                            @if($pelanggan_id) • Pelanggan: {{ \App\Models\Pelanggan::find($pelanggan_id)?->nama }} @endif
                            @if(!empty($status)) • Status: {{ count($status) }} dipilih @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <x-filament::button color="success" icon="heroicon-o-arrow-down-tray" wire:click="exportToExcel" size="sm">Export Excel</x-filament::button>
                    <x-filament::button color="danger" icon="heroicon-o-document-text" wire:click="exportToPdf" size="sm">Export PDF</x-filament::button>
                    <x-filament::button type="button" color="gray" size="sm" wire:click="resetFilters">Hapus Filter</x-filament::button>
                </div>
            </div>
        </x-filament::card>
        
        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->getSummaryData()['total_pesanan']) }}</div>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-shopping-cart class="w-8 h-8 text-blue-600 dark:text-blue-400"/>
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
                        <x-heroicon-o-currency-dollar class="w-8 h-8 text-green-600 dark:text-green-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Order</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($this->getSummaryData()['avg_order_value'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-purple-600 dark:text-purple-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Rate</div>
                        <div class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->getSummaryData()['completion_rate'], 1) }}%</div>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-check-circle class="w-8 h-8 text-blue-600 dark:text-blue-400"/>
                    </div>
                </div>
            </x-filament::card>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Pelanggan</h3>
                    <div class="relative h-[350px]">
                        <canvas id="topCustomersChart" data-chart-data="{{ json_encode($this->getTopCustomersData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Revenue 6 Bulan</h3>
                    <div class="relative h-[350px]">
                        <canvas id="monthlyRevenueChart" data-chart-data="{{ json_encode($this->getMonthlyRevenueData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Item (by Order)</h3>
                    <div class="relative h-[350px]">
                        <canvas id="topItemsChart" data-chart-data="{{ json_encode($this->getTopItemsData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Rute (by Trip)</h3>
                    <div class="relative h-[350px]">
                        <canvas id="topRoutesChart" data-chart-data="{{ json_encode($this->getTopRoutesData()) }}"></canvas>
                    </div>
                </div>
            </x-filament::card>
        </div>
        
        <x-filament::card>
            {{ $this->table }}
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
    window.initDashboardCharts = () => {
        if (typeof Chart === 'undefined') {
            setTimeout(window.initDashboardCharts, 500);
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

        // Top Customers
        const topCtx = document.getElementById('topCustomersChart');
        if (topCtx) {
            if (window.topChart) window.topChart.destroy();
            const dataRaw = topCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], revenue: [], count: [] };
            if (data.labels && data.labels.length > 0) {
                window.topChart = new Chart(topCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Revenue',
                                data: data.revenue,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Total Pesanan',
                                data: data.count,
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
                                        const val = ctx.dataset.yAxisID === 'y' ? formatRupiah(ctx.raw) : ctx.raw + ' order';
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

        // Monthly Revenue
        const monthCtx = document.getElementById('monthlyRevenueChart');
        if (monthCtx) {
            if (window.monthChart) window.monthChart.destroy();
            const dataRaw = monthCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], data: [] };
            if (data.labels && data.labels.length > 0) {
                window.monthChart = new Chart(monthCtx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: data.data,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (ctx) => 'Revenue: ' + formatRupiah(ctx.raw) } }
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

        // Top Items (by COUNT)
        const itemCtx = document.getElementById('topItemsChart');
        if (itemCtx) {
            if (window.itemChart) window.itemChart.destroy();
            const dataRaw = itemCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], count: [] };
            if (data.labels && data.labels.length > 0) {
                window.itemChart = new Chart(itemCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total Order',
                            data: data.count,
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (ctx) => 'Order: ' + ctx.raw } }
                        },
                        scales: {
                            y: { beginAtZero: true },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }

        // Top Routes (by COUNT)
        const routeCtx = document.getElementById('topRoutesChart');
        if (routeCtx) {
            if (window.routeChart) window.routeChart.destroy();
            const dataRaw = routeCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], count: [] };
            if (data.labels && data.labels.length > 0) {
                window.routeChart = new Chart(routeCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total Trip',
                            data: data.count,
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
                            tooltip: { callbacks: { label: (ctx) => 'Trip: ' + ctx.raw } }
                        },
                        scales: {
                            x: { beginAtZero: true },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    };

    window.initDashboardCharts();
    document.addEventListener('livewire:navigated', () => setTimeout(window.initDashboardCharts, 200));
    window.addEventListener('initCharts', () => setTimeout(window.initDashboardCharts, 200));
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => setTimeout(window.initDashboardCharts, 300));
    });
</script>
@endscript