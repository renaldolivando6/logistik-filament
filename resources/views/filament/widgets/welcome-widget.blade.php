<x-filament-widgets::widget>
    <div class="fi-wi-welcome relative overflow-hidden rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 p-6 shadow-lg">
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h2 class="text-2xl font-bold text-white">
                        Selamat Datang, {{ auth()->user()->name }}! 👋
                    </h2>
                    <p class="text-primary-100">
                        {{ now()->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>
                
                <div class="hidden md:block text-white/20">

                </div>
            </div>
        </div>
        
        <!-- Decorative circles -->
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-12 -left-12 h-48 w-48 rounded-full bg-white/5"></div>
    </div>
</x-filament-widgets::widget>