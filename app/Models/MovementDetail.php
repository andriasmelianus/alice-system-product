<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model {

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'movement_id',
        'weight',
        'length',
        'width',
        'height',
        'production_date',
        'expiration_date',
        'color',
        'version',
        'size',
        'model',
        'note',
    ];

    /**
     * Mendapatkan data movement
     */
    public function movement(){
        return $this->belongsTo('App\Models\Movement');
    }
}
