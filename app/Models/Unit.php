<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Relationship one-to-many pada tabel product.
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
}
