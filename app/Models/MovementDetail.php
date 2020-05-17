<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model {

    /**
     * Primary key pada tabel ini merupakan foreign key dari tabel movements.
     */
    protected $primaryKey = 'movement_id';

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
        'user',
    ];

    /**
     * Mendapatkan data movement
     */
    public function movement(){
        return $this->belongsTo('App\Models\Movement');
    }
}
