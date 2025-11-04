<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Sopir;
use App\Models\Kendaraan;
use App\Models\Rute;
use App\Models\KategoriBiaya;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== PELANGGAN (15 pelanggan) ====================
        $pelanggans = [
            ['kode' => 'CUST001', 'nama' => 'PT THE MASTER', 'alamat' => 'Waru, Surabaya', 'telepon' => '081234567890', 'kontak_person' => 'Bapak Joko'],
            ['kode' => 'CUST002', 'nama' => 'PT DTAPES TUNAS', 'alamat' => 'Negoro, Sidoarjo', 'telepon' => '082345678901', 'kontak_person' => 'Ibu Siti'],
            ['kode' => 'CUST003', 'nama' => 'PT TIMUR JAYA', 'alamat' => 'Pasuruan, Jawa Timur', 'telepon' => '083456789012', 'kontak_person' => 'Pak Budi'],
            ['kode' => 'CUST004', 'nama' => 'PT CITRA MANDIRI', 'alamat' => 'Gresik, Jawa Timur', 'telepon' => '084567890123', 'kontak_person' => 'Bu Ani'],
            ['kode' => 'CUST005', 'nama' => 'PT INDO KARYA', 'alamat' => 'Malang, Jawa Timur', 'telepon' => '085678901234', 'kontak_person' => 'Pak Hendra'],
            ['kode' => 'CUST006', 'nama' => 'PT SUKSES BERSAMA', 'alamat' => 'Mojokerto, Jawa Timur', 'telepon' => '086789012345', 'kontak_person' => 'Bu Rita'],
            ['kode' => 'CUST007', 'nama' => 'PT MAJU JAYA', 'alamat' => 'Bangkalan, Madura', 'telepon' => '087890123456', 'kontak_person' => 'Pak Agus'],
            ['kode' => 'CUST008', 'nama' => 'PT SENTOSA ABADI', 'alamat' => 'Lamongan, Jawa Timur', 'telepon' => '088901234567', 'kontak_person' => 'Bu Linda'],
            ['kode' => 'CUST009', 'nama' => 'PT CAHAYA TERANG', 'alamat' => 'Jombang, Jawa Timur', 'telepon' => '089012345678', 'kontak_person' => 'Pak Doni'],
            ['kode' => 'CUST010', 'nama' => 'PT BERKAH JAYA', 'alamat' => 'Tuban, Jawa Timur', 'telepon' => '081123456789', 'kontak_person' => 'Bu Sari'],
            ['kode' => 'CUST011', 'nama' => 'PT HARAPAN MULIA', 'alamat' => 'Bojonegoro, Jawa Timur', 'telepon' => '082234567890', 'kontak_person' => 'Pak Eko'],
            ['kode' => 'CUST012', 'nama' => 'PT KARYA MANDIRI', 'alamat' => 'Madiun, Jawa Timur', 'telepon' => '083345678901', 'kontak_person' => 'Bu Dewi'],
            ['kode' => 'CUST013', 'nama' => 'PT SEJAHTERA ABADI', 'alamat' => 'Kediri, Jawa Timur', 'telepon' => '084456789012', 'kontak_person' => 'Pak Roni'],
            ['kode' => 'CUST014', 'nama' => 'PT GLOBAL MAKMUR', 'alamat' => 'Blitar, Jawa Timur', 'telepon' => '085567890123', 'kontak_person' => 'Bu Maya'],
            ['kode' => 'CUST015', 'nama' => 'PT INDO CEMERLANG', 'alamat' => 'Banyuwangi, Jawa Timur', 'telepon' => '086678901234', 'kontak_person' => 'Pak Adi'],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create(array_merge($pelanggan, ['aktif' => true]));
        }

        // ==================== SOPIR (12 sopir) ====================
        $sopirs = [
            ['kode' => 'DRV001', 'nama' => 'Dedi Gunawan', 'telepon' => '085678901234', 'no_sim' => 'SIM123456', 'alamat' => 'Surabaya'],
            ['kode' => 'DRV002', 'nama' => 'Rudi Hartono', 'telepon' => '086789012345', 'no_sim' => 'SIM234567', 'alamat' => 'Sidoarjo'],
            ['kode' => 'DRV003', 'nama' => 'Ahmad Yani', 'telepon' => '087890123456', 'no_sim' => 'SIM345678', 'alamat' => 'Gresik'],
            ['kode' => 'DRV004', 'nama' => 'Bambang Sutrisno', 'telepon' => '088901234567', 'no_sim' => 'SIM456789', 'alamat' => 'Malang'],
            ['kode' => 'DRV005', 'nama' => 'Cahyo Prakoso', 'telepon' => '089012345678', 'no_sim' => 'SIM567890', 'alamat' => 'Pasuruan'],
            ['kode' => 'DRV006', 'nama' => 'Dimas Prasetyo', 'telepon' => '081123456789', 'no_sim' => 'SIM678901', 'alamat' => 'Mojokerto'],
            ['kode' => 'DRV007', 'nama' => 'Eko Widodo', 'telepon' => '082234567890', 'no_sim' => 'SIM789012', 'alamat' => 'Lamongan'],
            ['kode' => 'DRV008', 'nama' => 'Fajar Nugroho', 'telepon' => '083345678901', 'no_sim' => 'SIM890123', 'alamat' => 'Jombang'],
            ['kode' => 'DRV009', 'nama' => 'Gilang Ramadhan', 'telepon' => '084456789012', 'no_sim' => 'SIM901234', 'alamat' => 'Tuban'],
            ['kode' => 'DRV010', 'nama' => 'Hendra Wijaya', 'telepon' => '085567890123', 'no_sim' => 'SIM012345', 'alamat' => 'Bojonegoro'],
            ['kode' => 'DRV011', 'nama' => 'Iwan Setiawan', 'telepon' => '086678901234', 'no_sim' => 'SIM123450', 'alamat' => 'Madiun'],
            ['kode' => 'DRV012', 'nama' => 'Joko Susilo', 'telepon' => '087789012345', 'no_sim' => 'SIM234561', 'alamat' => 'Kediri'],
        ];

        foreach ($sopirs as $sopir) {
            Sopir::create(array_merge($sopir, ['aktif' => true]));
        }

        // ==================== KENDARAAN (10 kendaraan) ====================
        $kendaraans = [
            ['nopol' => 'B 1234 ZC', 'jenis' => 'Truk Engkel', 'kapasitas' => 5, 'merk' => 'Mitsubishi', 'tahun' => 2020],
            ['nopol' => 'B 5678 ZR', 'jenis' => 'Truk Tronton', 'kapasitas' => 15, 'merk' => 'Hino', 'tahun' => 2019],
            ['nopol' => 'B 1054 DOY', 'jenis' => 'Truk Box', 'kapasitas' => 8, 'merk' => 'Isuzu', 'tahun' => 2021],
            ['nopol' => 'L 9876 AB', 'jenis' => 'Truk Engkel', 'kapasitas' => 6, 'merk' => 'Mitsubishi', 'tahun' => 2018],
            ['nopol' => 'L 5432 CD', 'jenis' => 'Truk Tronton', 'kapasitas' => 12, 'merk' => 'Hino', 'tahun' => 2020],
            ['nopol' => 'N 8765 EF', 'jenis' => 'Truk Box', 'kapasitas' => 7, 'merk' => 'Isuzu', 'tahun' => 2019],
            ['nopol' => 'N 4321 GH', 'jenis' => 'Truk Engkel', 'kapasitas' => 5, 'merk' => 'Mitsubishi', 'tahun' => 2022],
            ['nopol' => 'B 7890 IJ', 'jenis' => 'Truk Tronton', 'kapasitas' => 14, 'merk' => 'Hino', 'tahun' => 2021],
            ['nopol' => 'L 2468 KL', 'jenis' => 'Truk Box', 'kapasitas' => 9, 'merk' => 'Isuzu', 'tahun' => 2020],
            ['nopol' => 'N 1357 MN', 'jenis' => 'Truk Engkel', 'kapasitas' => 6, 'merk' => 'Mitsubishi', 'tahun' => 2019],
        ];

        foreach ($kendaraans as $kendaraan) {
            Kendaraan::create(array_merge($kendaraan, ['aktif' => true]));
        }

        // ==================== RUTE (20 rute) ====================
        $rutes = [
            // Rute Asbes
            ['asal' => 'Waru', 'tujuan' => 'Gresik', 'jenis_muatan' => 'Asbes', 'harga_per_ton' => 31250],
            ['asal' => 'Negoro', 'tujuan' => 'Sidoarjo', 'jenis_muatan' => 'Asbes', 'harga_per_ton' => 30600],
            ['asal' => 'Surabaya', 'tujuan' => 'Bangkalan', 'jenis_muatan' => 'Asbes', 'harga_per_ton' => 32000],
            ['asal' => 'Waru', 'tujuan' => 'Mojokerto', 'jenis_muatan' => 'Asbes', 'harga_per_ton' => 29500],
            
            // Rute Besi
            ['asal' => 'Pasuruan', 'tujuan' => 'Bangkalan', 'jenis_muatan' => 'Besi', 'harga_per_ton' => 35000],
            ['asal' => 'Gresik', 'tujuan' => 'Malang', 'jenis_muatan' => 'Besi', 'harga_per_ton' => 38000],
            ['asal' => 'Sidoarjo', 'tujuan' => 'Pasuruan', 'jenis_muatan' => 'Besi', 'harga_per_ton' => 33500],
            ['asal' => 'Surabaya', 'tujuan' => 'Lamongan', 'jenis_muatan' => 'Besi', 'harga_per_ton' => 36000],
            
            // Rute Semen
            ['asal' => 'Surabaya', 'tujuan' => 'Malang', 'jenis_muatan' => 'Semen', 'harga_per_ton' => 28000],
            ['asal' => 'Gresik', 'tujuan' => 'Jombang', 'jenis_muatan' => 'Semen', 'harga_per_ton' => 27500],
            ['asal' => 'Tuban', 'tujuan' => 'Surabaya', 'jenis_muatan' => 'Semen', 'harga_per_ton' => 30000],
            ['asal' => 'Tuban', 'tujuan' => 'Malang', 'jenis_muatan' => 'Semen', 'harga_per_ton' => 32000],
            
            // Rute Material Bangunan
            ['asal' => 'Mojokerto', 'tujuan' => 'Surabaya', 'jenis_muatan' => 'Pasir', 'harga_per_ton' => 25000],
            ['asal' => 'Pasuruan', 'tujuan' => 'Sidoarjo', 'jenis_muatan' => 'Batu', 'harga_per_ton' => 24000],
            ['asal' => 'Bangkalan', 'tujuan' => 'Surabaya', 'jenis_muatan' => 'Batu', 'harga_per_ton' => 26000],
            
            // Rute Pallet/Kayu
            ['asal' => 'Gresik', 'tujuan' => 'Sidoarjo', 'jenis_muatan' => 'Pallet Kayu', 'harga_per_ton' => 29000],
            ['asal' => 'Lamongan', 'tujuan' => 'Surabaya', 'jenis_muatan' => 'Kayu', 'harga_per_ton' => 27000],
            
            // Rute Pupuk
            ['asal' => 'Gresik', 'tujuan' => 'Bojonegoro', 'jenis_muatan' => 'Pupuk', 'harga_per_ton' => 31000],
            ['asal' => 'Tuban', 'tujuan' => 'Madiun', 'jenis_muatan' => 'Pupuk', 'harga_per_ton' => 33000],
            
            // Rute Barang Umum
            ['asal' => 'Surabaya', 'tujuan' => 'Banyuwangi', 'jenis_muatan' => 'General Cargo', 'harga_per_ton' => 40000],
        ];

        foreach ($rutes as $rute) {
            Rute::create(array_merge($rute, ['aktif' => true]));
        }

        // ==================== KATEGORI BIAYA (tetap 6 kategori) ====================
        $kategori_biayas = [
            ['nama' => 'ACCU', 'keterangan' => 'Biaya aki/accu kendaraan'],
            ['nama' => 'SPAREPART', 'keterangan' => 'Biaya suku cadang kendaraan'],
            ['nama' => 'BAN', 'keterangan' => 'Biaya pembelian atau perbaikan ban'],
            ['nama' => 'SOLAR', 'keterangan' => 'Biaya bahan bakar solar'],
            ['nama' => 'SERVIS', 'keterangan' => 'Biaya servis rutin kendaraan'],
            ['nama' => 'TOL', 'keterangan' => 'Biaya tol perjalanan'],
        ];

        foreach ($kategori_biayas as $kategori) {
            KategoriBiaya::create(array_merge($kategori, ['aktif' => true]));
        }
    }
}