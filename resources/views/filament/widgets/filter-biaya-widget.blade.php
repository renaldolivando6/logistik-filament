<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🔍 Filter Laporan
        </x-slot>
        
        <x-slot name="description">
            Filter berdasarkan periode, kendaraan, dan kategori biaya
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Tanggal Mulai --}}
            <div class="space-y-2">
                <label for="start_date" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Tanggal Mulai
                    </span>
                </label>
                
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                    <div class="min-w-0 flex-1">
                        <input 
                            type="date" 
                            id="start_date"
                            wire:model.live="start_date"
                            class="fi-input block w-full border-none py-1.5 px-3 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-transparent [&::-webkit-calendar-picker-indicator]:dark:invert"
                        />
                    </div>
                </div>
            </div>
            
            {{-- Tanggal Akhir --}}
            <div class="space-y-2">
                <label for="end_date" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Tanggal Akhir
                    </span>
                </label>
                
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                    <div class="min-w-0 flex-1">
                        <input 
                            type="date" 
                            id="end_date"
                            wire:model.live="end_date"
                            class="fi-input block w-full border-none py-1.5 px-3 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-transparent [&::-webkit-calendar-picker-indicator]:dark:invert"
                        />
                    </div>
                </div>
            </div>
            
            {{-- Filter Kendaraan --}}
            <div class="space-y-2">
                <label for="kendaraan_id" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Kendaraan
                    </span>
                </label>
                
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                    <div class="min-w-0 flex-1">
                        <select 
                            id="kendaraan_id"
                            wire:model.live="kendaraan_id"
                            class="fi-input block w-full border-none py-1.5 px-3 text-base text-gray-950 outline-none transition duration-75 focus:ring-0 dark:text-white sm:text-sm sm:leading-6 bg-transparent"
                        >
                            <option value="">Semua Kendaraan</option>
                            @foreach(\App\Models\Kendaraan::where('aktif', true)->get() as $kendaraan)
                                <option value="{{ $kendaraan->id }}">{{ $kendaraan->nopol }} - {{ $kendaraan->jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            {{-- Filter Kategori Biaya --}}
            <div class="space-y-2">
                <label for="kategori_biaya_id" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Kategori Biaya
                    </span>
                </label>
                
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                    <div class="min-w-0 flex-1">
                        <select 
                            id="kategori_biaya_id"
                            wire:model.live="kategori_biaya_id"
                            class="fi-input block w-full border-none py-1.5 px-3 text-base text-gray-950 outline-none transition duration-75 focus:ring-0 dark:text-white sm:text-sm sm:leading-6 bg-transparent"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach(\App\Models\KategoriBiaya::where('aktif', true)->get() as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Info & Reset Button --}}
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">Periode:</span> 
                {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                @if($kendaraan_id)
                    <span class="ml-2">| <span class="font-medium">Kendaraan:</span> {{ \App\Models\Kendaraan::find($kendaraan_id)->nopol }}</span>
                @endif
                @if($kategori_biaya_id)
                    <span class="ml-2">| <span class="font-medium">Kategori:</span> {{ \App\Models\KategoriBiaya::find($kategori_biaya_id)->nama }}</span>
                @endif
            </div>
            
            @if($kendaraan_id || $kategori_biaya_id)
                <button 
                    wire:click="resetFilters"
                    type="button"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-danger fi-size-sm fi-btn-size-sm gap-1 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action"
                >
                    Reset Filter
                </button>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>