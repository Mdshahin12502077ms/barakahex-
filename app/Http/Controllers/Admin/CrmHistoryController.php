<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\CrmHistoryRepository;
use Illuminate\Http\Request;

class CrmHistoryController extends Controller
{

    public $crmHistoryRepository;
    public function __construct( CrmHistoryRepository $crmHistoryRepository){
        $this->crmHistoryRepository = $crmHistoryRepository;
    }

    public function index(){
        return view('admin.crm_history.index');
    }

    public function search(Request $request){
        $data  = $this->crmHistoryRepository->search($request->phone_number);
         
        if($data->count()>0){
            return response()->json([
            'status'=>true,
            
             'data'=>$data,
             
        ]);

        }else{
            return response()->json([
            'status'=>false,
            'message'=>'Data not found',
        ]);
        }

    }
}
