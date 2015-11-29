<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Validators\RestFormatter;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * 
     * @param string|null $code
     * @param string|null $msg
     * @param array $errors
     * @param int $status
     * @return \Response
     */
    protected function returnErrors($code = null, $msg = null, $errors = [], $status = 400)
    {
        if ( $errors instanceof Exception) {
            $errors = [$errors->getMessage()];
        }
        
        if (is_null($code)) {
            $code = 'unknow_error';
        }
        
        if (is_null($msg)) {
            $msg = 'Unknow error';
        }
        
        $results = [
            'code' => $code,
            'msg' => $msg,
        ];
        
        // More information about error
        if ($errors) {
            $results['errors'] = $errors;
        }
        
        return response()->json($results, $status);
    }
    
    /**
     * Return responses with 404 code
     * 
     * @param string $code
     * @param string $msg
     * @return type
     */
    protected function resourceNotFound($code = null, $msg = null, $errors = null)
    {
        if (is_null($code)) {
            $code = 'resource_not_found';
        }
        
        if (is_null($msg)) {
            $msg = 'Resource not found';
        }
        return $this->returnErrors($code, $msg, $errors, 404);
    }

    /**
     * 
     * @param \Illuminate\Validation\Validator $validator
     * @return \Response
     */
    protected function returnValidationErrors($validator)
    {
        return $this->returnErrors('validation_failed', 'Validation failed, check errors to get more info', (new RestFormatter($validator))->errors());
    }
}
