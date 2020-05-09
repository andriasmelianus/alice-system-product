<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'product_id',
        'filename',
        'is_default',
        'user',
    ];
    public $timestamps = FALSE;

    /**
     * Relationship many-to-one pada tabel product.
     */
    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}
