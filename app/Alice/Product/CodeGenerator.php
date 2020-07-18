<?php

/**
 * Class ini digunakan untuk menghasilkan kode secara otomatis.
 * Apabila user tidak menginputkan kode produk ketika input data produk.
 * Format kode yang baru adalah:
 *
 * AA00000
 *
 * 2 karakter yang pertama adalah huruf yang diambil dari nama produk.
 *      apabila nama produk hanya terdiri dari 1 kata, maka 2 huruf pertama dari kata tersebut dijadikan kode.
 *      apabila nama produk terdiri lebih dari 1 kata, maka huruf pertama diambil dari huruf pertama dari kata
 *      pertama dan huruf kedua diambil dari huruf pertama dari kata kedua.
 *
 * 5 karakter berikutnya merupakan nomor urut berdasarkan data yang tersimpan di dalam database.
 */

namespace App\Alice\Product;

use App\Models\Product;

class CodeGenerator
{

    /**
     * Konstruktor
     */
    public function __construct()
    {
    }

    /**
     * Menghasilkan kode yang baru
     *
     * @param String $productName
     * @return String $newCode
     */
    public function newCode($productName)
    {
        $arrProductName = explode(" ", $productName);
        $twoCharsNewCode = '';
        $intLastNumber = 0; //
        $newCode = '';
        if (\count($arrProductName) == 1) {
            // Bila nama produk yang baru mengandung hanya 1 kata
            $twoCharsNewCode = \substr($arrProductName[0], 0, 2);
        } else {
            // Bila nama produk yang baru mengandung lebih dari 1 kata
            $twoCharsNewCode = \substr($arrProductName[0], 0, 1) . \substr($arrProductName[1], 0, 1);
        }

        // Ubah menjadi huruf besar
        $twoCharsNewCode = strtoupper($twoCharsNewCode);

        $currentProduct = Product::where('code', 'LIKE', $twoCharsNewCode . '_____')->orderBy('code', 'DESC');
        if ($currentProduct->count() > 0) {
            $strCurrentCode = $currentProduct->first()->code;
            $intCurrentCode = ((int) \substr($strCurrentCode, 2, 5)) + 1;
            $newCode = $twoCharsNewCode . \sprintf('%05d', $intCurrentCode);
        } else {
            $newCode = $twoCharsNewCode . '00001';
        }

        return $newCode;
    }
}
