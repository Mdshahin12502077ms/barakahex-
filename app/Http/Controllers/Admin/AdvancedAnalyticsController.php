<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\AdvancedAnalyticsRepository;
use Illuminate\Http\Request;

class AdvancedAnalyticsController extends Controller
{

    protected $advancedRepository;
    public function __construct(AdvancedAnalyticsRepository $advancedRepository)
    {
        $this->advancedRepository = $advancedRepository;
    }
    public function operationAnalytics(Request $request){
        $data=$this->advancedRepository->AdvanceOpretion($request);
         
        
      return view('backend.admin.advanced.operation-analytics',['data'=>$data]);
    }

    public function riderAnalytics(Request $request){
        $data=$this->advancedRepository->riderAnalytics($request);
        return view('backend.admin.advanced.rider-analytics', ['data' => $data]);
    }
}
