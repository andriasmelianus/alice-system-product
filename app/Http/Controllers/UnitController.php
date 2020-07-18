<?php

namespace App\Http\Controllers;

use App\Alice\ApiResponser;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    private $apiResponser;
    private $unit;

    /**
     * Create controller instance
     */
    public function __construct(ApiResponser $apiResponser, Unit $unit)
    {
        $this->apiResponser = $apiResponser;
        $this->unit = $unit;
    }

    /**
     * Membaca data unit
     * Untuk dipasangkan pada control autocomplete
     *
     * @param Request $request
     * @return Array
     */
    public function get(Request $request)
    {
        $units = $this->unit->where('name', 'LIKE', $request->keyword . '%')->get();
        return $this->apiResponser->success($units);
    }
}
