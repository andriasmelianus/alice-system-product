<?php
namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Models\Serial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SerialController extends Controller {
    private $apiResponser;
    private $rules = [
        'movement_id' => 'required|integer',
        'number' => 'max:127'
    ];
    private $serial;

    /**
     * Create controller instance
     */
    public function __construct(ApiResponser $apiResponser, Serial $serial){
        $this->apiResponser = $apiResponser;
        $this->serial = $serial;
    }

    /**
     * Menambah data serial
     *
     * @param Request $request
     * @return Array
     */
    public function create(Request $request){
        $this->validate($request, $this->rules);
        $serial = Serial::create($request->all());

        return $this->apiResponser->success($serial);
    }

    /**
     * Membaca data serial
     * Untuk dipasangkan pada control autocomplete
     *
     * @param Request $request
     * @return Array
     */
    public function get(Request $request){
        $serials = $this->serial->where('number', 'LIKE', $request->keyword.'%')->get();
        return $this->apiResponser->success($serials);
    }

    /**
     * Mengubah data serial
     *
     * @param Request $request
     * @return Array
     */
    public function update(Request $request){
        $this->validate($request, $this->rules);

        $serial = Serial::findOrFail($request->id);
        $serial->fill($request->all());

        if($serial->isClean()){
            return $this->errorResponse('Tidak ada perubahan data', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $serial->save();
        return $this->apiResponser->success($serial);
    }

    /**
     * Menghapus data serial dari database
     *
     * @param Request $request
     * @return Array
     */
    public function destroy(Request $request){
        $serial = Serial::findOrFail($request->id);
        $serial->delete();

        return $this->apiResponser->success($serial);
    }
}
