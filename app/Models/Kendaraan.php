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
        'kapasitas' => 'integer',
        'tahun' => 'integer',
        'aktif' => 'boolean',
    ];
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
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