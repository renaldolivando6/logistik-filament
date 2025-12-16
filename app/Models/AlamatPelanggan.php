<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlamatPelanggan extends Model
{
    use SoftDeletes;

    protected $table = 'alamat_pelanggan';

    protected $fillable = [
        'pelanggan_id',
        'label',
        'alamat_lengkap',
        'kota',
        'provinsi',
        'kode_pos',
        'kontak_person',
        'telepon',
        'is_default',
        'aktif',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'aktif' => 'boolean',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getAlamatLengkapFormatAttribute(): string
    {
        $parts = array_filter([
            $this->label,
            $this->alamat_lengkap,
            $this->kota,
            $this->provinsi,
            $this->kode_pos,
        ]);

        return implode(', ', $parts);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForPelanggan($query, int $pelangganId)
    {
        return $query->where('pelanggan_id', $pelangganId);
    }
}