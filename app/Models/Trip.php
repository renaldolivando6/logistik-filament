<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;
    
    protected $table = 'trip';
    
    protected $fillable = [
        'tanggal_trip',
        'sopir_id',
        'kendaraan_id',
        'uang_sangu',
        'uang_kembali',
        'status_sangu',
        'tanggal_pengembalian',
        'catatan_sangu',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_trip' => 'date',
        'tanggal_pengembalian' => 'date',
        'uang_sangu' => 'decimal:2',
        'uang_kembali' => 'decimal:2',
    ];
    
    // ========================================
    // RELATIONSHIPS
    // ========================================
    
    public function sopir(): BelongsTo
    {
        return $this->belongsTo(Sopir::class);
    }
    
    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }
    
    public function suratJalan(): HasMany
    {
        return $this->hasMany(SuratJalan::class);
    }
    
    public function biayaOperasional(): HasMany
    {
        return $this->hasMany(BiayaOperasional::class);
    }
    
    // ========================================
    // ACCESSORS
    // ========================================
    
    public function getTotalBeratAttribute(): float
    {
        return $this->suratJalan()->sum('berat_dikirim');
    }
    
    /**
     * Get total biaya operasional for this trip
     */
    public function getTotalBiayaOperasionalAttribute(): float
    {
        return $this->biayaOperasional()->sum('jumlah');
    }
    
    /**
     * Calculate sisa uang sangu (auto)
     */
    public function getSisaSanguAttribute(): float
    {
        return $this->uang_sangu - $this->total_biaya_operasional;
    }
    
    // ========================================
    // SCOPES
    // ========================================
    
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeBelumSelesai($query)
    {
        return $query->whereIn('status', ['draft', 'berangkat']);
    }
}