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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dari Tanggal Pesanan</label>
                        <input type="date" wire:model="dari_tanggal" class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sampai Tanggal Pesanan</label>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Pesanan</label>
                        <select wire:model="pesanan_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Pesanan</option>
                            @foreach($this->getPesananOptions() as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Trip</label>
                        <select wire:model="trip_id" class="fi-select block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm transition duration-75">
                            <option value="">Semua Trip</option>
                            @foreach($this->getTripOptions() as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status Surat Jalan</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($this->getStatusSJOptions() as $key => $label)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-all hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 {{ in_array($key, $status_sj) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input type="checkbox" wire:model="status_sj" value="{{ $key }}" class="fi-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" wire:click="$set('status_sj', ['draft', 'dikirim', 'diterima', 'batal'])" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">✓ Pilih Semua</button>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <button type="button" wire:click="$set('status_sj', [])" class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 font-medium">✕ Hapus Semua</button>
                        </div>
                    </div>
                    
                    <div class="col-span-full">
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all {{ $tampilkan_selesai ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400' }}">
                            <input type="checkbox" wire:model="tampilkan_selesai" class="fi-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="ml-3">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tampilkan Pesanan yang Sudah 100% Terkirim</span>
                                <span class="block text-xs text-gray-600 dark:text-gray-400 mt-1">Default: Hanya tampilkan outstanding pesanan (progress < 100%)</span>
                            </span>
                        </label>
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
        <x-filament::card class="mb-6 bg-gray-50 dark:bg-gray-800 border-l-4 border-gray-700 dark:border-gray-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Filter Aktif</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
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
                            @if(!empty($status_sj)) • Status: {{ count($status_sj) }} dipilih @endif
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
        
        <div class="flex gap-1 mb-4">
            <x-filament::card class="flex-1 border border-gray-200 dark:border-gray-700 p-1.5">
                <div class="text-center">
                    <div class="text-[9px] font-semibold uppercase text-gray-500 dark:text-gray-400">Total SJ</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($this->getSummaryData()['total_sj']) }}</div>
                </div>
            </x-filament::card>
            
            <x-filament::card class="flex-1 border border-gray-200 dark:border-gray-700 p-1.5">
                <div class="text-center">
                    <div class="text-[9px] font-semibold uppercase text-gray-500 dark:text-gray-400">Berat</div>
                    <div class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($this->getSummaryData()['total_berat'], 0, ',', '.') }}</div>
                </div>
            </x-filament::card>

            <x-filament::card class="flex-1 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-1.5">
                <div class="text-center">
                    <div class="text-[9px] font-semibold uppercase text-gray-500 dark:text-gray-400">Draft</div>
                    <div class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ number_format($this->getSummaryData()['sj_draft']) }}</div>
                </div>
            </x-filament::card>

            <x-filament::card class="flex-1 border border-gray-200 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20 p-1.5">
                <div class="text-center">
                    <div class="text-[9px] font-semibold uppercase text-gray-600 dark:text-gray-400">Dikirim</div>
                    <div class="text-lg font-bold text-yellow-700 dark:text-yellow-500">{{ number_format($this->getSummaryData()['sj_dikirim']) }}</div>
                </div>
            </x-filament::card>

            <x-filament::card class="flex-1 border-2 border-green-600 dark:border-green-500 bg-green-50 dark:bg-green-900/20 p-1.5">
                <div class="text-center">
                    <div class="text-[9px] font-semibold uppercase text-gray-600 dark:text-gray-400">Diterima</div>
                    <div class="text-lg font-bold text-green-600 dark:text-green-500">{{ number_format($this->getSummaryData()['sj_diterima']) }}</div>
                </div>
            </x-filament::card>
        </div>
        
        <x-filament::card class="mb-6">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">Outstanding Pesanan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">ID Pesanan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Total Berat</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">SJ Dibuat</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Terkirim</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Sisa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Progress</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Jml SJ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->getOutstandingPesanan() as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $item['id'] }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($item['tanggal_pesanan'])->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item['pelanggan'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $item['item'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ number_format($item['total_berat'], 2, ',', '.') }} Kg</td>
                                <td class="px-4 py-3 text-right text-blue-600 dark:text-blue-400">{{ number_format($item['berat_sj_dibuat'], 2, ',', '.') }} Kg</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">{{ number_format($item['berat_terkirim'], 2, ',', '.') }} Kg</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $item['sisa_berat'] > 0 ? 'text-red-600 dark:text-red-500' : 'text-green-600 dark:text-green-500' }}">
                                    {{ number_format($item['sisa_berat'], 2, ',', '.') }} Kg
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 max-w-[80px]">
                                            <div class="h-2 rounded-full {{ $item['persen_terkirim'] >= 100 ? 'bg-green-600' : ($item['persen_terkirim'] > 0 ? 'bg-blue-600' : 'bg-gray-400') }}" style="width: {{ min($item['persen_terkirim'], 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $item['persen_terkirim'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $item['jumlah_sj'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded 
                                        {{ $item['status'] === 'selesai' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : '' }}
                                        {{ $item['status'] === 'dalam_perjalanan' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-500' : '' }}
                                        {{ $item['status'] === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : '' }}
                                        {{ $item['status'] === 'batal' ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400' : '' }}
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $item['status'])) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                    Tidak ada data pesanan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Silakan pilih filter dan klik "Tampilkan Laporan" untuk melihat tracking pesanan & trip.</p>
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>