<?php

namespace App\Traits;
use Illuminate\Support\Facades\Validator;

trait ApiReturnFormatTrait {

    protected function setMessage($message='', $type ='success'){
        session()->flush('message',$message);
        session()->flush('type',$type);
    }

    protected function validateWithJson($data=[], $rules =[]){
        $validator=Validator::make($data,$rules);

        if($validator->passes()){
            return true;
        }
        return response()->json($validator->errors(), 422);
    }

    protected function responseWithSuccess($message='', $url='', $data=[], $code =200){
        return response()->json([
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
            'url'       => $url,
        ],$code);
    }

    protected function responseWithError($message='', $url='', $data=[], $code=null){
        if($code==null){
            $code=400;
        }
        return response()->json([
            'error'     => true,
            'message'   => $message,
            'data'      => $data,
            'url'       => $url,
        ],$code);
    }
}
