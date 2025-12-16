<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;
    
    protected $table = 'pelanggan';
    
    protected $fillable = [
        'kode',
        'nama',
        // 'alamat', // ❌ DROP setelah migration
        'telepon',
        'kontak_person',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    // ========================================
    // EXISTING RELATIONSHIPS
    // ========================================
    
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
    
    // ========================================
    // ✅ NEW RELATIONSHIPS - Alamat Pelanggan
    // ========================================
    
    public function alamat(): HasMany
    {
        return $this->hasMany(AlamatPelanggan::class);
    }
    
    public function alamatAktif(): HasMany
    {
        return $this->hasMany(AlamatPelanggan::class)->where('aktif', true);
    }
    
    public function alamatDefault(): HasOne
    {
        return $this->hasOne(AlamatPelanggan::class)->where('is_default', true);
    }
}