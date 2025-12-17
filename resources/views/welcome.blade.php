<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trans Anugerah Nusa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-4xl w-full">
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-8 py-16 md:px-16 md:py-24 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
                </div>
                <div class="relative z-10">
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 tracking-tight">Trans Anugerah Nusa</h1>
                    <p class="text-gray-300 text-lg md:text-xl max-w-2xl mx-auto">Sistem Manajemen Logistik Modern & Terintegrasi</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="px-8 py-12 md:px-16 md:py-16">
                <!-- Feature Grid -->
                <div class="grid md:grid-cols-3 gap-6 mb-12">
                    <div class="border border-gray-200 rounded-xl p-6 hover:border-gray-900 hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl font-bold text-gray-900 mb-2">Management Pesanan</div>
                        <p class="text-gray-600">Kelola pesanan dan pengiriman</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-6 hover:border-gray-900 hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl font-bold text-gray-900 mb-2">Management Aset</div>
                        <p class="text-gray-600">Kelola kendaraan dan dan armada</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-6 hover:border-gray-900 hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl font-bold text-gray-900 mb-2">Analisa</div>
                        <p class="text-gray-600">Dashboard dan laporan komprehensif</p>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center pt-8 border-t border-gray-200">
                    <p class="text-gray-600 mb-8 text-lg">Akses panel administrasi untuk mengelola sistem</p>
                    <a href="https://trans-anugerah-nusa.dazytech.web.id/admin" 
                       class="inline-block bg-gray-900 text-white px-12 py-4 rounded-xl font-semibold text-lg hover:bg-gray-800 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                        Masuk Admin Panel
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500 text-sm">
            <p>&copy; 2024 Logistik Trans Anugerah Nusa by Dazytech. All rights reserved.</p>
        </div>
    </div>
</body>
</html>