<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use SoftDeletes;

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'brand',
        'unit_id',
        'is_service',
        'is_expire',
        'is_serialized',
        'accounting_method',
        'description',
        'user',
    ];

    /**
     * Relationship many-to-one pada tabel unit.
     */
    public function unit(){
        return $this->belongsTo('App\Models\Unit');
    }

    /**
     * Relationship one-to-many pada tabel image.
     */
    public function images(){
        return $this->hasMany('App\Models\Image');
    }

    /**
     * Relationship many-to-many pada tabel category.
     */
    public function categories(){
        return $this->belongsToMany('App\Models\Category');
    }

    /**
     * Relasi pada tabel yang menyimpan mutasi barang.
     * Relationship one-to-many pada tabel movement.
     */
    public function movements(){
        return $this->hasMany('App\Models\Movement');
    }
}
