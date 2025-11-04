<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rute extends Model
{
    use SoftDeletes;
    
    protected $table = 'rute';
    
    protected $fillable = [
        'asal',
        'tujuan',
        'jenis_muatan',
        'harga_per_ton',
        'aktif',
    ];
    
    protected $casts = [
        'harga_per_ton' => 'decimal:2',
        'aktif' => 'boolean',
    ];
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}