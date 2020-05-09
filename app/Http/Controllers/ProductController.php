<?php

namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Alice\GuidGeneratorTrait;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use DB;

class ProductController extends Controller
{
    use GuidGeneratorTrait;

    // Class properties
    private $apiResponser;
    private $rules;
    private $imageRules;
    private $product;
    private $category;
    private $image;
    private $brand;
    // Image handling
    private $uploadedImageIconPath = 'images/icon/';
    private $uploadedImageSmallPath = 'images/small/';
    private $uploadedImageMediumPath = 'images/medium/';
    private $uploadedImageOriginalPath = 'images/original/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ApiResponser $apiResponser,
        Product $product,
        Category $category,
        Image $image,
        Brand $brand
    ){
        $this->apiResponser = $apiResponser;
        $this->rules = [
            'company_id'=>'required',
            'code'=>'max:127',
            'name'=>'required|max:127',
            'brand'=>'max:127',
            'unit_id'=>'integer',
            'is_service'=>'boolean',
            'is_serialized'=>'boolean',
            'accounting_method'=>'string',
            'user'=>'max:127',
        ];
        $this->imageRules = [
            'image'=> 'image',
            'product_id' => 'required',
            'filename'=>'max:255',
            'user'=>'max:127',
        ];
        $this->product = $product;
        $this->category = $category;
        $this->image = $image;
        $this->brand = $brand;
    }

    /**
     * Menambah data product
     * Struktur data produk yang diterima:
     * [
     *  code: ...
     *  name: ...
     *  categories: [
     *      [name: ...],
     *      [name: ...],
     *      ...
     *  ]
     * ]
     *
     * @param Request $request
     * @return Json
     */
    public function create(Request $request){
        $theRules = $this->rules;
        // Rule supaya code unique
        $theRules['code'] = ['max:127', Rule::unique('products', 'code')->where(function($query) use($request){
            return $query->whereNull('deleted_at')->where('company_id', $request->company_id);
        })];
        // Rule supaya name unique
        $theRules['name'] = ['required', 'max:127', Rule::unique('products', 'name')->where(function($query) use($request){
            return $query->whereNull('deleted_at')->where('company_id', $request->company_id);
        })];
        $this->validate($request, $theRules);
        $productData = $request->all();

        // Extract dulu data category dan dapatkan ID-nya
        $productCategories = $productData['categories'];
        $categoryIds = $this->extractCategories($productData['categories']);
        unset($productData['categories']);
        // Extract data brand
        if(isset($request->brand)) {$this->extractBrand($request->brand);}

        // Insert data produk
        $product = Product::create($productData);
        // Attach category pada data product
        $product->categories()->detach();
        $product->categories()->attach($categoryIds);

        return $this->apiResponser->success($product, Response::HTTP_CREATED);
    }

    /**
     * Membaca data product
     *
     * @param Request $request
     * @return void
     */
    public function read(Request $request){
        $keyword = $request->input('keyword').'%';
        $products = Product::where('name', 'LIKE', $keyword)->limit(10)->get();

        return $this->apiResponser->success($products);
    }

    /**
     * Mengupdate data product
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request){
        $updateRules = $this->rules;
        unset($updateRules['company_id']);
        $updateRules['name'] = 'sometimes|required|max:127';
        $this->validate($request, $updateRules);

        $product = Product::findOrFail($request->id);
        $product->fill($request->all());

        $categoryIds=$this->extractCategories($request->categories);
        $product->categories()->detach();
        $product->categories()->attach($categoryIds);
        if(isset($request->brand)) {$this->extractBrand($request->brand);}

        if($product->isClean()){
            return $this->apiResponser->error('Tidak ada perubahan data', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product->save();
        return $this->apiResponser->success($product);
    }

    /**
     * Menghapus data product
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request){
        $product = Product::findOrFail($request->input('id'));
        $product->delete();

        return $this->apiResponser->success($product);
    }


    /**
     * Menambah gambar pada produk
     *
     * @param Request $request
     * @return String image path
     */
    public function addImage(Request $request){
        $this->validate($request, $this->imageRules);

        // Pisahkan image stream dari request
        $uploadedOriginalImage = $request->file('original');
        $uploadedIconImage = $request->file('icon');
        $uploadedSmallImage = $request->file('small');
        $uploadedMediumImage = $request->file('medium');
        $uploadedLargeImage = $request->file('large');
        // Dapatkan nama file yang asli
        $uploadedImageFilename = $uploadedOriginalImage->getClientOriginalName();
        // Pindahkan image yang diupload pada folder yang disediakan
        $uploadedOriginalImage->move('images/original/', $uploadedImageFilename);
        $uploadedIconImage->move('images/icon/', $uploadedImageFilename);
        $uploadedSmallImage->move('images/small/', $uploadedImageFilename);
        $uploadedMediumImage->move('images/medium/', $uploadedImageFilename);
        $uploadedLargeImage->move('images/large/', $uploadedImageFilename);

        $imageData = [
            'product_id' => $request->product_id,
            'filename' => $uploadedImageFilename,
            'user' => $request->user
        ];
        $defaultImageExists = Image::where("is_default", 1)->count();
        if(!$defaultImageExists){
            $imageData['is_default'] = TRUE;
        }

        $image = Image::create($imageData);
        return $this->apiResponser->success($image);
    }

    /**
     * Menjadikan gambar sebagai gambar utama produk.
     *
     * @param Request $request
     * @return void
     */
    public function setDefaultImage(Request $request){
        $this->validate($request, $this->imageRules);

        $image = FALSE;
        if($this->image->where('product_id', $request->product_id)->where('id', $request->id)->count()){
            // Update is_default pada produk ini menjadi FALSE semua
            $this->image->where('product_id', $request->product_id)
                ->update(['is_default' => FALSE]);

            // Baru update is_default=TRUE pada gambar yang dipilih.
            $image = $this->image->where('id', $request->id)
                ->update(['is_default' => TRUE]);
        }else{
            return $this->apiResponser->error('Gambar tidak ditemukan.', 422);
        }

        return $this->apiResponser->success($image);
    }

    /**
     * Menghapus gmabar dari produk
     *
     * @param Request $request
     * @return void
     */
    public function removeImage(Request $request){
        $this->validate($request, $this->imageRules);

        $image = $this->image->where('id', $request->id)->first();
        // Supaya tidak dapat menghapus gambar sembarangan
        if($request->product_id == $image->product_id){
            // Periksa apakah gambar merupakan gambar utama
            if($image->is_default){
                return $this->apiResponser->error('Gambar utama produk tidak dapat dihapus.', 422);
            }

            // Proses hapus gambar
            $image = $image = $this->image->where('id', $request->id)->delete();
        }else{
            return $this->apiResponser->error('Gambar tidak ditemukan.', 422);
        }

        return $this->apiResponser->success($image);
    }


    /**
     * Memasukkan data category satu per satu dan mendapatkan masing-masing IDnya.
     */
    public function extractCategories($categories=[]){
        $categoryIds = [];
        foreach($categories AS $category){
            $category = Category::firstOrCreate([
                'name' => $category
            ]);
            array_push($categoryIds, $category->id);
        }

        return $categoryIds;
    }

    /**
     * Masukkan data brand pada tabel brands.
     * Tabel brands digunakan untuk autocomplete.
     */
    public function extractBrand($brand){
        $brandData = Brand::firstOrCreate([
            'name' => $brand
        ]);
        return $brandData->id;
    }
}
