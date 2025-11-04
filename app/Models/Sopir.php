<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sopir extends Model
{
    use SoftDeletes;
    
    protected $table = 'sopir';
    
    protected $fillable = [
        'kode',
        'nama',
        'telepon',
        'no_sim',
        'alamat',
        'aktif',
    ];
    
    protected $casts = [
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
}