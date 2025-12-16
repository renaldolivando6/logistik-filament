<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesanan extends Model
{
    use SoftDeletes;
    
    protected $table = 'pesanan';
    
    protected $fillable = [
        'tanggal_pesanan',
        'pelanggan_id',
        'kendaraan_id',
        'sopir_id',
        'rute_id',
        'berat',
        'harga_per_kg',
        'total_tagihan',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_pesanan' => 'date',
        'berat' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
    ];
    
    // ✅ Accessor untuk jenis_muatan via relasi
    public function getJenisMuatanAttribute()
    {
        return $this->rute?->item?->nama;
    }
    
    // ✅ Accessor untuk total berat yang sudah dikirim via surat jalan
    public function getTotalBeratDikirimAttribute()
    {
        return $this->suratJalan()->sum('berat_dikirim');
    }
    
    // ✅ Accessor untuk sisa berat yang belum dikirim
    public function getSisaBeratAttribute()
    {
        return $this->berat - $this->total_berat_dikirim;
    }
    
    // Relationships
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
    
    // ❌ REMOVED: uangSangu() - tidak ada lagi
    
    public function biayaOperasional()
    {
        return $this->hasMany(BiayaOperasional::class);
    }
    
    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class);
    }
}