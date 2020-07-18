<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'movement_id_start',
        'movement_id_end',
        'quantity',
    ];

    /**
     * Relationship many-to-one pada tabel movement.
     */
    public function movement_start()
    {
        return $this->belongsTo('App\Models\Movement', 'movement_id_start');
    }

    /**
     * Relationship many-to-one pada tabel movement.
     */
    public function movement_end()
    {
        return $this->belongsTo('App\Models\Movement', 'movement_id_end');
    }
}
