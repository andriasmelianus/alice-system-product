<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serial extends Model {

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'movement_id',
        'number',
    ];

    /**
     * Relationship many-to-one pada tabel movement.
     */
    public function movement(){
        return $this->belongsTo('App\Models\Movement');
    }
}
