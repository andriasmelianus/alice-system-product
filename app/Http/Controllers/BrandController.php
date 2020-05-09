<?php
namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller {
    private $apiResponser;
    private $brand;

    /**
     * Create controller instance
     */
    public function __construct(ApiResponser $apiResponser, Brand $brand){
        $this->apiResponser = $apiResponser;
        $this->brand = $brand;
    }

    /**
     * Membaca data brand
     * Untuk dipasangkan pada control autocomplete
     *
     * @param Request $request
     * @return Array
     */
    public function get(Request $request){
        $brands = $this->brand->where('name', 'LIKE', $request->keyword.'%')->get();
        return $this->apiResponser->success($brands);
    }
}
