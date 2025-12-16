<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;
    
    protected $table = 'kendaraan';
    
    protected $fillable = [
        'nopol',
        'jenis',
        'kapasitas',
        'merk',
        'tahun',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
    
    // ❌ REMOVED: uangSangu()
    
    public function trip()
    {
        return $this->hasMany(Trip::class);
    }
}