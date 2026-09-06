<?php

namespace App\Http\Controllers\DeliveryMan;

use App\DataTables\Admin\DeliveryMan\ReturnTask;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\DeliveryMan\ReturnTaskRepository;
class ReturnTaskController extends Controller
{

    protected $returnTaskRepo;

    public function __construct(ReturnTaskRepository $returnTaskRepo)
    {
        $this->returnTaskRepo = $returnTaskRepo;
    }


    public function index(ReturnTask $dataTable)
    {
        return $dataTable->render('deliveryman.return_tasks.index');
    }

    public function complete($id)
    {
       $data= $this->returnTaskRepo->complete($id);
        if($data['success'] == true){
            Toastr::success($data['message']);
        }else{
            Toastr::error($data['message']);
        }

        return redirect()->back();
    }
}
