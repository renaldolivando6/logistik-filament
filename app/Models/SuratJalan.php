<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratJalan extends Model
{
    use SoftDeletes;
    
    protected $table = 'surat_jalan';
    
    protected $fillable = [
        'trip_id',
        'pesanan_id',
        'alamat_pelanggan_id', // ✅ NEW
        'tanggal_kirim',
        'tanggal_terima',
        'berat_dikirim',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_terima' => 'date',
        'berat_dikirim' => 'decimal:2',
    ];
    
    // ========================================
    // EXISTING RELATIONSHIPS
    // ========================================
    
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
    
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
    
    // ========================================
    // ✅ NEW RELATIONSHIP - Alamat Pelanggan
    // ========================================
    
    public function alamatPelanggan(): BelongsTo
    {
        return $this->belongsTo(AlamatPelanggan::class);
    }
    
    // ========================================
    // EXISTING ACCESSORS
    // ========================================
    
    public function getRuteAttribute()
    {
        return $this->pesanan?->rute;
    }
    
    public function getPelangganAttribute()
    {
        return $this->pesanan?->pelanggan;
    }
    
    public function getJenisMuatanAttribute()
    {
        return $this->pesanan?->jenis_muatan;
    }
    
    // ========================================
    // ✅ USEFUL SCOPES
    // ========================================
    
    public function scopeBelumMasukTrip($query)
    {
        return $query->whereNull('trip_id');
    }
    
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}