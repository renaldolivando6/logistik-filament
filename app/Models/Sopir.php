<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sopir extends Model
{
    use SoftDeletes;
    
    protected $table = 'sopir';
    
    protected $fillable = [
        'nama',
        'telepon',
        'no_sim',
        'alamat',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    // ✅ Auto-generate kode saat create (untuk backward compatibility)
    protected static function booted(): void
    {
        static::creating(function ($sopir) {
            if (empty($sopir->kode)) {
                $sopir->kode = 'DRV' . str_pad(self::count() + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
    
    public function uangSangu()
    {
        return $this->hasMany(UangSangu::class);
    }
}