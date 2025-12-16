<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangSangu extends Model
{
    use SoftDeletes;
    
    protected $table = 'uang_sangu';
    
    protected $fillable = [
        'tanggal_sangu',
        'pesanan_id',
        'trip_id', // ✅ NEW
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
    
    // ✅ NEW: Relation to Trip
    public function trip()
    {
        return $this->belongsTo(Trip::class);
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