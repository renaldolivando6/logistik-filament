<x-filament-panels::page>
    {{-- Header Stats --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Pesanan</p>
                    <h3 class="text-2xl font-bold mt-1">{{ \App\Models\Pesanan::count() }}</h3>
                </div>
                <x-filament::icon icon="heroicon-o-document-text" class="w-10 h-10 opacity-50" />
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Dalam Perjalanan</p>
                    <h3 class="text-2xl font-bold mt-1">{{ \App\Models\Pesanan::where('status', 'dalam_perjalanan')->count() }}</h3>
                </div>
                <x-filament::icon icon="heroicon-o-truck" class="w-10 h-10 opacity-50" />
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Selesai</p>
                    <h3 class="text-2xl font-bold mt-1">{{ \App\Models\Pesanan::where('status', 'selesai')->count() }}</h3>
                </div>
                <x-filament::icon icon="heroicon-o-check-circle" class="w-10 h-10 opacity-50" />
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Tagihan</p>
                    <h3 class="text-lg font-bold mt-1">{{ 'Rp ' . number_format(\App\Models\Pesanan::sum('total_tagihan'), 0, ',', '.') }}</h3>
                </div>
                <x-filament::icon icon="heroicon-o-banknotes" class="w-10 h-10 opacity-50" />
            </div>
        </div>
    </div>
</x-filament-panels::page>