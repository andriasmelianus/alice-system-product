<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model {
    use SoftDeletes;

    /**
     * Kolom-kolom yang dapat diisi
     */
    protected $fillable = [
        'branch_id',
        'date',
        'type',
        'code',
        'product_id',
        'product',
        'quantity',
        'unit',
        'user',
    ];

    /**
     * Mendapatkan data detil dari sebuah movement
     */
    public function detail(){
        return $this->hasOne('App\Models\MovementDetail');
    }

    /**
     * Relationship many-to-one pada tabel product.
     */
    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Relationship one-to-many pada tabel serial.
     */
    public function serials(){
        return $this->hasMany('App\Models\Serial');
    }

    /**
     * Relationship one-to-many pada tabel allocation.
     */
    public function allocationStart(){
        return $this->hasMany('App\Models\Allocation', 'movement_id_start');
    }

    /**
     * Relationship one-to-many pada tabel allocation.
     */
    public function allocationEnd(){
        return $this->hasMany('App\Models\Allocation', 'movement_id_end');
    }


    // SCOPES
    public function scopeBranch($query, $branchId){
        return $query->where('branch_id', $branchId);
    }
    public function scopeProduct($query, $productId){
        return $query->where('product_id', $productId);
    }
}
