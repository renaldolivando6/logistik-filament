<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span>Filter Periode</span>
            </div>
        </x-slot>
        
        <x-slot name="description">
            Pilih rentang tanggal untuk melihat laporan
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        </div>
        
        {{-- Info Periode --}}
        <div class="mt-4 flex items-center gap-3 rounded-lg bg-primary-50 dark:bg-primary-500/10 px-4 py-2.5 border border-primary-100 dark:border-primary-500/20">
            <div class="text-sm">
                <span class="font-semibold text-primary-700 dark:text-primary-300">Periode:</span> 
                <span class="text-primary-600 dark:text-primary-400 font-medium">
                    {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                </span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>