<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'name',
    ];
    public $timestamps = FALSE;
}
