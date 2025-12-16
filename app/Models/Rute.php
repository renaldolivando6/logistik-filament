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
        'item_id',
        'harga_per_kg',
        'aktif',
    ];
    
    protected $casts = [
        'harga_per_kg' => 'decimal:2',
        'aktif' => 'boolean',
    ];
    
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
    
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}