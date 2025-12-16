<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;
    
    protected $table = 'trip';
    
    protected $fillable = [
        'tanggal_trip',
        'sopir_id',
        'kendaraan_id',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_trip' => 'date',
    ];
    
    // Relationships
    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }
    
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
    
    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class);
    }
    
    public function uangSangu()
    {
        return $this->hasOne(UangSangu::class);
    }
}