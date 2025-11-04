<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaOperasional extends Model
{
    use SoftDeletes;
    
    protected $table = 'biaya_operasional';
    
    protected $fillable = [
        'tanggal_biaya',
        'pesanan_id',
        'kendaraan_id',
        'kategori_biaya_id',
        'jumlah',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal_biaya' => 'date',
        'jumlah' => 'decimal:2',
    ];
    
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
    
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
    
    public function kategoriBiaya()
    {
        return $this->belongsTo(KategoriBiaya::class);
    }
}