<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaOperasional extends Model
{
    use SoftDeletes;
    
    protected $table = 'biaya_operasional';
    
    protected $fillable = [
        'tanggal_biaya',
        'trip_id',
        'pesanan_id',
        'kendaraan_id',
        'kategori_biaya_id',
        'jumlah',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal_biaya' => 'date',
        'jumlah' => 'decimal:2',
    ];
    
    // ========================================
    // RELATIONSHIPS
    // ========================================
    
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
    
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
    
    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }
    
    public function kategoriBiaya(): BelongsTo
    {
        return $this->belongsTo(KategoriBiaya::class);
    }
    
    // ========================================
    // ACCESSORS
    // ========================================
    
    /**
     * Get tipe biaya (TRIP or NON-TRIP)
     */
    public function getTipeBiayaAttribute(): string
    {
        return $this->trip_id ? 'TRIP' : 'NON-TRIP';
    }
    
    // ========================================
    // SCOPES
    // ========================================
    
    public function scopeTerkaitTrip($query)
    {
        return $query->whereNotNull('trip_id');
    }
    
    public function scopeBiayaUmum($query)
    {
        return $query->whereNull('trip_id');
    }
}