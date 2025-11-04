<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangSangu extends Model
{
    use SoftDeletes;
    
    protected $table = 'uang_sangu';
    
    protected $fillable = [
        'nomor_sangu',
        'tanggal_sangu',
        'pesanan_id',
        'sopir_id',
        'kendaraan_id',
        'jumlah',
        'catatan',
        'status',
    ];
    
    protected $casts = [
        'tanggal_sangu' => 'date',
        'jumlah' => 'decimal:2',
    ];
    
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
    
    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }
    
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}