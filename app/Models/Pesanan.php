<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesanan extends Model
{
    use SoftDeletes;
    
    protected $table = 'pesanan';
    
    protected $fillable = [
        'nomor_pesanan',
        'tanggal_pesanan',
        'pelanggan_id',
        'kendaraan_id',
        'sopir_id',
        'rute_id',
        'jenis_muatan',
        'tonase',
        'harga_per_ton',
        'total_tagihan',
        'uang_sangu',
        'sisa_tagihan',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_pesanan' => 'date',
        'tonase' => 'decimal:2',
        'harga_per_ton' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'uang_sangu' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
    ];
    
    // Auto-create Uang Sangu saat Pesanan dibuat
    protected static function booted(): void
    {
        static::created(function ($pesanan) {
            if ($pesanan->uang_sangu > 0) {
                UangSangu::create([
                    'nomor_sangu' => 'SNG-' . date('Ymd') . '-' . str_pad(UangSangu::count() + 1, 4, '0', STR_PAD_LEFT),
                    'tanggal_sangu' => $pesanan->tanggal_pesanan,
                    'pesanan_id' => $pesanan->id,
                    'sopir_id' => $pesanan->sopir_id,
                    'kendaraan_id' => $pesanan->kendaraan_id,
                    'jumlah' => $pesanan->uang_sangu,
                    'catatan' => 'Auto-generated dari Pesanan #' . $pesanan->nomor_pesanan,
                    'status' => 'disetujui',
                ]);
            }
        });
        
        // Update Uang Sangu kalau Pesanan di-update
        static::updated(function ($pesanan) {
            $uangSangu = UangSangu::where('pesanan_id', $pesanan->id)->first();
            
            if ($pesanan->uang_sangu > 0) {
                if ($uangSangu) {
                    // Update existing
                    $uangSangu->update([
                        'tanggal_sangu' => $pesanan->tanggal_pesanan,
                        'sopir_id' => $pesanan->sopir_id,
                        'kendaraan_id' => $pesanan->kendaraan_id,
                        'jumlah' => $pesanan->uang_sangu,
                    ]);
                } else {
                    // Create new
                    UangSangu::create([
                        'nomor_sangu' => 'SNG-' . date('Ymd') . '-' . str_pad(UangSangu::count() + 1, 4, '0', STR_PAD_LEFT),
                        'tanggal_sangu' => $pesanan->tanggal_pesanan,
                        'pesanan_id' => $pesanan->id,
                        'sopir_id' => $pesanan->sopir_id,
                        'kendaraan_id' => $pesanan->kendaraan_id,
                        'jumlah' => $pesanan->uang_sangu,
                        'catatan' => 'Auto-generated dari Pesanan #' . $pesanan->nomor_pesanan,
                        'status' => 'disetujui',
                    ]);
                }
            } elseif ($uangSangu) {
                // Hapus kalau uang_sangu jadi 0
                $uangSangu->delete();
            }
        });
    }
    
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
    
    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }
    
    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }
    
    public function uangSangu()
    {
        return $this->hasMany(UangSangu::class);
    }
    
    public function biayaOperasional()
    {
        return $this->hasMany(BiayaOperasional::class);
    }
}