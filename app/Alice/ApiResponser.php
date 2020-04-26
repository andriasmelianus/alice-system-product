<?php
namespace App\Alice;

use Illuminate\Http\Response;

class ApiResponser {
    private $contentType;

    public function __construct($contentType='application/json'){
        $this->contentType = $contentType;
    }

    public function success($data, $code = Response::HTTP_OK){
        return response($data, $code)
            ->header('Content-Type', $this->contentType);
    }

    public function error($message, $code){
        return response()
            ->json([
                'error' => $message,
                'code' => $code
            ], $code);
    }

    public function errorMessage($message, $code){
        return response($message, $code)
            ->header('Content-Type', $this->contentType);
    }
}
