<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratJalan extends Model
{
    use SoftDeletes;
    
    protected $table = 'surat_jalan';
    
    protected $fillable = [
        'trip_id',
        'pesanan_id',
        'tanggal_kirim',
        'tanggal_terima',
        'tonase_dikirim',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_terima' => 'date',
        'tonase_dikirim' => 'decimal:2',
    ];
    
    // Relationships
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
    
    // Accessor untuk rute (via pesanan)
    public function getRuteAttribute()
    {
        return $this->pesanan?->rute;
    }
}