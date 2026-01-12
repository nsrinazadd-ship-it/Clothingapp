<?php

namespace App\Traits;

trait GeneralTrait
{
    /**
     * Send a success response
     */
    public function returnSuccessMessage($message = "تم بنجاح", $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $code);
    }
    /**
     * Return data with success response
     */
    public function returnData($key, $value, $message = "", $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            $key => $value,
        ], $code);
    }

    /**
     * Return error message
     */
    public function returnError($message = "حدث خطأ ما", $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * Return validation errors
     */
    public function returnValidationError($validator, $code = 422)
    {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], $code);
    }

    /**
     * Return unauthorized access
     */
    public function returnUnauthorized($message = "غير مصرح", $code = 401)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }
}
