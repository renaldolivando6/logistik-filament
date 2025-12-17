<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="w-5 h-5 text-primary-500"/>
                <span>Quick Actions</span>
            </div>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Buat Pesanan --}}
            <a href="{{ route('filament.admin.resources.pesanan.pesanans.create') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-primary-500 dark:hover:border-primary-400 transition-all hover:shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/40 transition-colors">
                                <x-heroicon-o-shopping-cart class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Pesanan</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Input pesanan baru dari pelanggan</p>
                    </div>
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400 group-hover:text-primary-500 group-hover:translate-x-1 transition-all"/>
                </div>
            </a>
            
            {{-- Buat Surat Jalan --}}
            <a href="{{ route('filament.admin.resources.surat-jalan.surat-jalans.create') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-primary-500 dark:hover:border-primary-400 transition-all hover:shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg group-hover:bg-green-200 dark:group-hover:bg-green-800/40 transition-colors">
                                <x-heroicon-o-document-text class="w-6 h-6 text-green-600 dark:text-green-400"/>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Surat Jalan</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Siapkan surat jalan untuk pengiriman</p>
                    </div>
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400 group-hover:text-primary-500 group-hover:translate-x-1 transition-all"/>
                </div>
            </a>
            
            {{-- Buat Trip --}}
            <a href="{{ route('filament.admin.resources.trip.trips.create') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-primary-500 dark:hover:border-primary-400 transition-all hover:shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg group-hover:bg-orange-200 dark:group-hover:bg-orange-800/40 transition-colors">
                                <x-heroicon-o-truck class="w-6 h-6 text-orange-600 dark:text-orange-400"/>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Trip</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Assign sopir dan kendaraan untuk trip</p>
                    </div>
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400 group-hover:text-primary-500 group-hover:translate-x-1 transition-all"/>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>