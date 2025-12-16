<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    
    protected $table = 'item';
    
    protected $fillable = [
        'nama',
        'satuan',
        'keterangan',
        'aktif',
    ];
    
    protected $casts = [
        'aktif' => 'boolean',
    ];
    
    public function rute()
    {
        return $this->hasMany(Rute::class, 'item_id');
    }
}