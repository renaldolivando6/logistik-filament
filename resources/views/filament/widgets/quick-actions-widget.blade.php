<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚡ Quick Actions
        </x-slot>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Pesanan Baru --}}
            <a href="{{ \App\Filament\Resources\Pesanan\PesananResource::getUrl('create') }}" 
               class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-500/10 dark:to-primary-600/10 rounded-lg border-2 border-primary-200 dark:border-primary-500/20 hover:border-primary-400 dark:hover:border-primary-400 transition-all hover:scale-105 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-500 text-white group-hover:bg-primary-600 transition">
                    {{-- ✅ Pakai Filament Icon --}}
                    <x-filament::icon
                        icon="heroicon-o-plus"
                        class="w-6 h-6"
                    />
                </div>
                <span class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Pesanan Baru</span>
            </a>
            
            {{-- Laporan Penjualan --}}
            <a href="{{ \App\Filament\Pages\Reports\SalesReport::getUrl() }}" 
               class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-success-50 to-success-100 dark:from-success-500/10 dark:to-success-600/10 rounded-lg border-2 border-success-200 dark:border-success-500/20 hover:border-success-400 dark:hover:border-success-400 transition-all hover:scale-105 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-500 text-white group-hover:bg-success-600 transition">
                    {{-- ✅ Pakai Filament Icon --}}
                    <x-filament::icon
                        icon="heroicon-o-chart-bar"
                        class="w-6 h-6"
                    />
                </div>
                <span class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Laporan</span>
            </a>
            
            {{-- Master Data - Pelanggan --}}
            <a href="{{ \App\Filament\Resources\Pelanggan\PelangganResource::getUrl('index') }}" 
               class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-warning-50 to-warning-100 dark:from-warning-500/10 dark:to-warning-600/10 rounded-lg border-2 border-warning-200 dark:border-warning-500/20 hover:border-warning-400 dark:hover:border-warning-400 transition-all hover:scale-105 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-500 text-white group-hover:bg-warning-600 transition">
                    {{-- ✅ Pakai Filament Icon --}}
                    <x-filament::icon
                        icon="heroicon-o-user-group"
                        class="w-6 h-6"
                    />
                </div>
                <span class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Master Data</span>
            </a>
            
            {{-- Biaya Operasional --}}
            <a href="{{ \App\Filament\Resources\BiayaOperasional\BiayaOperasionalResource::getUrl('index') }}" 
               class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-danger-50 to-danger-100 dark:from-danger-500/10 dark:to-danger-600/10 rounded-lg border-2 border-danger-200 dark:border-danger-500/20 hover:border-danger-400 dark:hover:border-danger-400 transition-all hover:scale-105 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-danger-500 text-white group-hover:bg-danger-600 transition">
                    {{-- ✅ Pakai Filament Icon --}}
                    <x-filament::icon
                        icon="heroicon-o-banknotes"
                        class="w-6 h-6"
                    />
                </div>
                <span class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Biaya Ops</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>