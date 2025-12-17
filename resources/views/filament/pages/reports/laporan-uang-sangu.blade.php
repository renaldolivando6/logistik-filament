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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dari Tanggal</label>
                        <input type="date" wire:model="dari_tanggal" class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sampai Tanggal</label>
                        <input type="date" wire:model="sampai_tanggal" class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Pengembalian</label>
                        <select wire:model="status_sangu" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Status</option>
                            @foreach($this->getStatusSanguOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
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
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kendaraan</label>
                        <select wire:model="kendaraan_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Kendaraan</option>
                            @foreach($this->getKendaraanOptions() as $id => $nopol)
                                <option value="{{ $id }}">{{ $nopol }}</option>
                            @endforeach
                        </select>
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
        <x-filament::card class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-900 dark:text-green-200">Filter Aktif</p>
                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                            @if($dari_tanggal && $sampai_tanggal)
                                Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}
                            @elseif($dari_tanggal)
                                Dari: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d/m/Y') }}
                            @elseif($sampai_tanggal)
                                Sampai: {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d/m/Y') }}
                            @else
                                Semua Periode
                            @endif
                            @if($status_sangu) • Status: {{ $this->getStatusSanguOptions()[$status_sangu] }} @endif
                            @if($sopir_id) • Sopir: {{ \App\Models\Sopir::find($sopir_id)?->nama }} @endif
                            @if($kendaraan_id) • Kendaraan: {{ \App\Models\Kendaraan::find($kendaraan_id)?->nopol }} @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <x-filament::button color="success" icon="heroicon-o-arrow-down-tray" wire:click="exportToExcel" size="sm">
                        <span class="hidden sm:inline">Export Excel</span>
                        <span class="sm:hidden">Excel</span>
                    </x-filament::button>
                    <x-filament::button color="danger" icon="heroicon-o-document-text" wire:click="exportToPdf" size="sm">
                        <span class="hidden sm:inline">Export PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </x-filament::button>
                </div>
            </div>
        </x-filament::card>
        
        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sangu Diberikan</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($this->getSummaryData()['total_sangu_diberikan'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-banknotes class="w-8 h-8 text-blue-600 dark:text-blue-400"/>
                    </div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Biaya</div>
                        <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($this->getSummaryData()['total_biaya_operasional'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-heroicon-o-receipt-percent class="w-8 h-8 text-red-600 dark:text-red-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Harus Dikembalikan</div>
                        <div class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($this->getSummaryData()['total_harus_kembali'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <x-heroicon-o-arrow-uturn-left class="w-8 h-8 text-orange-600 dark:text-orange-400"/>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah Dikembalikan</div>
                        <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($this->getSummaryData()['total_sudah_kembali'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <x-heroicon-o-check-circle class="w-8 h-8 text-green-600 dark:text-green-400"/>
                    </div>
                </div>
            </x-filament::card>
        </div>
        
        <x-filament::card class="mb-6">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 10 Trip - Status Pengembalian</h3>
                <div class="relative h-[400px]">
                    <canvas id="outstandingSanguChart" data-chart-data="{{ json_encode($this->getOutstandingSanguData()) }}"></canvas>
                </div>
            </div>
        </x-filament::card>
        
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
    window.initSanguCharts = () => {
        if (typeof Chart === 'undefined') {
            setTimeout(window.initSanguCharts, 500);
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

        // Outstanding Sangu Chart (Horizontal Bar)
        const barCtx = document.getElementById('outstandingSanguChart');
        if (barCtx) {
            if (window.sanguChart) window.sanguChart.destroy();
            const dataRaw = barCtx.getAttribute('data-chart-data');
            const data = dataRaw ? JSON.parse(dataRaw) : { labels: [], belum_kembali: [], sudah_kembali: [] };
            
            if (data.labels && data.labels.length > 0) {
                window.sanguChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Belum Dikembalikan',
                                data: data.belum_kembali,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Sudah Dikembalikan',
                                data: data.sudah_kembali,
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { 
                                position: 'top',
                                labels: { font: { size: 12 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': ' + formatRupiah(ctx.raw)
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                stacked: false,
                                ticks: { 
                                    callback: (val) => 'Rp ' + (val / 1000000).toFixed(1) + 'jt' 
                                }
                            },
                            y: { 
                                stacked: false,
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }
    };

    window.initSanguCharts();
    document.addEventListener('livewire:navigated', () => setTimeout(window.initSanguCharts, 200));
    window.addEventListener('initCharts', () => setTimeout(window.initSanguCharts, 200));
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => setTimeout(window.initSanguCharts, 300));
    });
</script>
@endscript