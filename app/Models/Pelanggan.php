<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;
    
    protected $table = 'pelanggan';
    
    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'telepon',
        'kontak_person',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}