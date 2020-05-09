<?php

namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Models\MovementType;
use App\Models\Movement;
use App\Models\MovementDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MovementController extends Controller
{
    private $apiResponser;
    private $rules = [
        'branch_id' => 'required|integer',
        'date' => 'required|date',
        'type' => 'max:127',
        'code' => 'max:127',
        'product_id' => 'required|integer',
        'product' => 'max:127',
        'quantity' => 'required|integer',
        'unit' => 'max:127',
        'user' => 'max:127',
    ];
    private $movementDetailRules = [
        'color' => 'max:127',
        'weight' => 'integer',
        'length' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'production_date' => 'date',
        'expiration_date' => 'date',
        'version' => 'max:127',
        'size' => 'max:127',
        'model' => 'max:127',
    ];
    private $movement;
    private $movementDetail;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ApiResponser $apiResponser, Movement $movement, MovementDetail $movementDetail){
        $this->apiResponser = $apiResponser;
        $this->movement = $movement;
        $this->movementDetail = $movementDetail;
    }

    /**
     * Menambah data movement
     *
     * @param Request $request
     * @return Json
     */
    public function create(Request $request){
        $this->validate($request, $this->rules);
        $movementData = $request->all();
        $movement = Movement::create($movementData);

        return $this->apiResponser->success($movement, Response::HTTP_CREATED);
    }

    /**
     * Membaca data movement
     *
     * @param Request $request
     * @return void
     */
    public function read(Request $request){
        $movements = \DB::table('v_movements')->where('product_id', $request->product_id)
            ->whereBetween('date', [$request->date_start, $request->date_end])
            ->get();

        return $this->apiResponser->success($movements);
    }

    /**
     * DATA MOVEMENT TIDAK DAPAT DIUPDATE
     *
     * @param Request $request
     * @return void
     */
    // public function update(Request $request){
    //     $this->validate($request, $this->rules);

    //     $movement = Movement::findOrFail($request->input('id'));
    //     $movement->fill($request->all());

    //     if($movement->isClean()){
    //         return $this->errorResponse('Tidak ada perubahan data', Response::HTTP_UNPROCESSABLE_ENTITY);
    //     }

    //     $movement->save();
    //     return $this->apiResponser->success($movement);
    // }

    /**
     * Menghapus data movement
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request){
        $movement = Movement::findOrFail($request->input('id'));
        $movement->delete();

        return $this->apiResponser->success($movement);
    }

    /**
     * Membaca data movement type dari dalam database
     *
     * @return Array
     */
    public function readType(){
        $movementTypes = MovementType::all();

        return $this->apiResponser->success($movementTypes);
    }


    /**
     * Menambahkan data detail movement
     *
     * @param Request $request
     * @return Array
     */
    public function createDetail(Request $request){
        $this->validate($request, $this->movementDetailRules);
        $movementDetail = MovementDetail::create($request->all());

        return $this->apiResponser->success($movementDetail);
    }

    /**
     * Menambahkan detil movement
     * Terutama pada produk yang dapat kadaluarsa
     *
     * @param Request $request
     * @return Array
     */
    public function readDetail(Request $request){
        $movementDetail = $this->movementDetail->where('movement_id', $request->movement_id)->first();

        return $this->apiResponser->success($movementDetail);
    }

    /**
     * Mengupdate data movement detail
     *
     * @param Request $request
     * @return Array
     */
    public function updateDetail(Request $request){
        $this->validate($request, $this->movementDetailRules);

        $movementDetail = MovementDetail::findOrFail($request->movement_id);
        $movementDetail->fill($request->all());

        if($movementDetail->isClean()){
            return $this->errorResponse('Tidak ada perubahan data', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $movementDetail->save();
        return $this->apiResponser->success($movementDetail);
    }

    /**
     * Menghapus data movement detail
     *
     * @param Request $request
     * @return Array
     */
    public function destroyDetail(Request $request){
        $movementDetail = MovementDetail::findOrFail($request->movement_id);
        $movementDetail->delete();

        return $this->apiResponser->success($movementDetail);
    }
}
