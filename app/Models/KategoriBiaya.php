<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriBiaya extends Model
{
    use SoftDeletes;
    
    protected $table = 'kategori_biaya';
    
    protected $fillable = [
        'nama',
        'keterangan',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    public function biayaOperasional()
    {
        return $this->hasMany(BiayaOperasional::class);
    }
}