<?php
namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller {
    private $apiResponser;
    private $product;
    private $category;

    /**
     * Create controller instance
     */
    public function __construct(ApiResponser $apiResponser, Product $product, Category $category){
        $this->apiResponser = $apiResponser;
        $this->product = $product;
        $this->category = $category;
    }

    /**
     * Menambahkan data category ke dalam tabel categories
     * Dan menambahkannya kepada intermediate table category_product.
     * Maka dari itu, data yang dikirimkan juga HARUS mengandung product_id.
     *
     * @param Request $request
     * @return Boolean
     */
    public function add(Request $request){
        $category = $this->category->firstOrCreate([
            'name' => $request->category
        ]);

        $product = $this->product->where('id', $request->product_id)->first();
        $product->categories()->attach($category->id);

        return $this->apiResponser->success($category);
    }

    /**
     * Membaca data category
     * Untuk dipasangkan pada control autocomplete
     *
     * @param Request $request
     * @return Array
     */
    public function get(Request $request){
        $categories = $this->category->where('name', 'LIKE', $request->keyword.'%')->get();
        return $this->apiResponser->success($categories);
    }

    /**
     * Menghapus data category dari intermediate table category_product.
     * Function ini TIDAK menghapus data dari tabel categories.
     *
     * @param Request $request
     * @return Boolean
     */
    public function remove(Request $request){
        $product = $this->product->where('id', $request->product_id)->first();
        $product->categories()->detach($request->id);

        return $this->apiResponser->success(TRUE);
    }
}
