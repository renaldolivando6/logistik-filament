<?php

namespace Database\Seeders;

use App\Models\Pesanan;
use App\Models\UangSangu;
use App\Models\BiayaOperasional;
use App\Models\Pelanggan;
use App\Models\Sopir;
use App\Models\Kendaraan;
use App\Models\Rute;
use App\Models\KategoriBiaya;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating transaksi data...');
        
        // Ambil data master
        $pelanggans = Pelanggan::all();
        $sopirs = Sopir::all();
        $kendaraans = Kendaraan::all();
        $rutes = Rute::all();
        $kategori_biayas = KategoriBiaya::all();

        // Generate data untuk 3 bulan terakhir
        $startDate = now()->subMonths(3)->startOfMonth();
        $endDate = now();
        
        $pesananCounter = 1;
        $sanguCounter = 1;
        $totalPesanan = 0;
        $totalBiaya = 0;
        $totalSangu = 0;

        // Loop per minggu untuk generate data
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Generate 3-5 pesanan per minggu
            $jumlahPesanan = rand(3, 5);
            
            for ($i = 0; $i < $jumlahPesanan; $i++) {
                // Random date dalam minggu ini
                $tanggalPesanan = $currentDate->copy()->addDays(rand(0, 6));
                
                if ($tanggalPesanan > $endDate) {
                    break;
                }
                
                // Random data
                $pelanggan = $pelanggans->random();
                $sopir = $sopirs->random();
                $kendaraan = $kendaraans->random();
                $rute = $rutes->random();
                
                // Hitung values - REALISTIS
                $maxTonase = $kendaraan->kapasitas;
                $tonase = rand(
                    (int)($maxTonase * 0.7), 
                    (int)($maxTonase * 0.9)
                );
                
                $hargaPerTon = $rute->harga_per_ton;
                $totalTagihan = $tonase * $hargaPerTon;
                
                // Uang Sangu: 15-25% dari total tagihan
                $uangSangu = rand(
                    (int)($totalTagihan * 0.15), 
                    (int)($totalTagihan * 0.25)
                );
                
                $sisaTagihan = $totalTagihan - $uangSangu;
                
                // Status random - mostly selesai
                $rand = rand(1, 100);
                if ($rand <= 75) {
                    $status = 'selesai';
                } elseif ($rand <= 90) {
                    $status = 'dalam_perjalanan';
                } elseif ($rand <= 97) {
                    $status = 'draft';
                } else {
                    $status = 'batal';
                }
                
                // Buat Pesanan
                $pesanan = Pesanan::create([
                    'nomor_pesanan' => 'PO-' . $tanggalPesanan->format('Ymd') . '-' . str_pad($pesananCounter, 4, '0', STR_PAD_LEFT),
                    'tanggal_pesanan' => $tanggalPesanan,
                    'pelanggan_id' => $pelanggan->id,
                    'kendaraan_id' => $kendaraan->id,
                    'sopir_id' => $sopir->id,
                    'rute_id' => $rute->id,
                    'jenis_muatan' => $rute->jenis_muatan,
                    'tonase' => $tonase,
                    'harga_per_ton' => $hargaPerTon,
                    'total_tagihan' => $totalTagihan,
                    'uang_sangu' => $uangSangu,
                    'sisa_tagihan' => $sisaTagihan,
                    'status' => $status,
                    'catatan' => $rute->asal . ' → ' . $rute->tujuan,
                ]);
                
                $pesananCounter++;
                $totalPesanan++;
                
                // ✅ MANUAL INSERT Uang Sangu
                if ($uangSangu > 0) {
                    UangSangu::create([
                        'nomor_sangu' => 'SNG-' . $tanggalPesanan->format('Ymd') . '-' . str_pad($sanguCounter, 4, '0', STR_PAD_LEFT),
                        'tanggal_sangu' => $tanggalPesanan,
                        'pesanan_id' => $pesanan->id,
                        'sopir_id' => $sopir->id,
                        'kendaraan_id' => $kendaraan->id,
                        'jumlah' => $uangSangu,
                        'catatan' => 'Uang sangu untuk ' . $pesanan->nomor_pesanan,
                        'status' => 'disetujui',
                    ]);
                    $sanguCounter++;
                    $totalSangu++;
                }
                
                // Generate Biaya Operasional - REALISTIS (20-35% dari revenue)
                if (in_array($status, ['dalam_perjalanan', 'selesai'])) {
                    
                    // Target total biaya: 20-35% dari total tagihan
                    $targetBiaya = rand(
                        (int)($totalTagihan * 0.20), 
                        (int)($totalTagihan * 0.35)
                    );
                    
                    // Biaya WAJIB: Solar (60-70% dari budget biaya)
                    $biayaSolar = (int)($targetBiaya * rand(60, 70) / 100);
                    BiayaOperasional::create([
                        'tanggal_biaya' => $tanggalPesanan,
                        'pesanan_id' => $pesanan->id,
                        'kendaraan_id' => $kendaraan->id,
                        'kategori_biaya_id' => $kategori_biayas->where('nama', 'SOLAR')->first()->id,
                        'jumlah' => $biayaSolar,
                        'keterangan' => 'Solar - ' . $pesanan->nomor_pesanan,
                    ]);
                    $totalBiaya++;
                    
                    // Biaya TOL (10-15% dari budget biaya)
                    $biayaTol = (int)($targetBiaya * rand(10, 15) / 100);
                    BiayaOperasional::create([
                        'tanggal_biaya' => $tanggalPesanan,
                        'pesanan_id' => $pesanan->id,
                        'kendaraan_id' => $kendaraan->id,
                        'kategori_biaya_id' => $kategori_biayas->where('nama', 'TOL')->first()->id,
                        'jumlah' => $biayaTol,
                        'keterangan' => 'Tol - ' . $pesanan->nomor_pesanan,
                    ]);
                    $totalBiaya++;
                    
                    // Biaya OPTIONAL (15-20% dari budget biaya) - tidak selalu ada
                    $sisaBudget = $targetBiaya - $biayaSolar - $biayaTol;
                    
                    if (rand(1, 100) <= 40) { // 40% chance ada biaya tambahan
                        $kategoriOpsional = ['SPAREPART', 'SERVIS', 'BAN', 'ACCU'];
                        $kategoriTerpilih = $kategoriOpsional[array_rand($kategoriOpsional)];
                        
                        $biayaTambahan = rand(
                            (int)($sisaBudget * 0.5),
                            (int)($sisaBudget * 0.8)
                        );
                        
                        BiayaOperasional::create([
                            'tanggal_biaya' => $tanggalPesanan->copy()->addDay(),
                            'pesanan_id' => $pesanan->id,
                            'kendaraan_id' => $kendaraan->id,
                            'kategori_biaya_id' => $kategori_biayas->where('nama', $kategoriTerpilih)->first()->id,
                            'jumlah' => $biayaTambahan,
                            'keterangan' => $kategoriTerpilih . ' - ' . $pesanan->nomor_pesanan,
                        ]);
                        $totalBiaya++;
                    }
                }
                
                // Progress indicator setiap 10 pesanan
                if ($totalPesanan % 10 == 0) {
                    $this->command->info("Progress: {$totalPesanan} pesanan, {$totalSangu} uang sangu, {$totalBiaya} biaya created...");
                }
            }
            
            // Next week
            $currentDate->addWeek();
        }
        
        $this->command->info("✓ SELESAI!");
        $this->command->info("  - {$totalPesanan} Pesanan");
        $this->command->info("  - {$totalSangu} Uang Sangu");
        $this->command->info("  - {$totalBiaya} Biaya Operasional");
    }
}