<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'name',
    ];
    public $timestamps = FALSE;

    /**
     * Relationship one-to-many pada tabel product.
     */
    public function products(){
        return $this->belongsToMany('App\Models\Product');
    }
}
