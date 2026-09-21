<?php

namespace App\Http\Controllers\DeliveryMan;

use App\DataTables\Admin\DeliveryMan\ReturnTask;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\User;
use App\Traits\SendNotification;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\DeliveryMan\ReturnTaskRepository;
class ReturnTaskController extends Controller
{
    use SendNotification;


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
            // Notification Logic
            $parcel = Parcel::with('merchant.user')->find($id);
            if ($parcel && $parcel->merchant && $parcel->merchant->user) {
                $staff = User::where('user_type', 'staff')->get();
                $merchant = User::where('id', $parcel->merchant->user->id)->get();
                $users = $staff->merge($merchant);
                $title = 'Return Task Completed';
                $details = 'Your parcel (ID: ' . $parcel->parcel_no . ') has been successfully returned to you by our rider.';
                $this->sendNotification($title, $users, $details, ['parcel_read'], 'info', url('merchant/parcel/details/' . $parcel->id), '');
            }

            Toastr::success($data['message']);
        }else{
            Toastr::error($data['message']);
        }

        return redirect()->back();
    }
    public function resendOtp($id)
    {
        $response = $this->returnTaskRepo->resendOtp($id);
        if ($response['success'] ?? ($response['status'] ?? false)) {
            Toastr::success($response['message'] ?? __('otp_sent_successfully'));
        } else {
            Toastr::error($response['message'] ?? __('something_went_wrong_please_try_again'));
        }
        return redirect()->back();
    }

    public function verifyOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $response = $this->returnTaskRepo->verifyOtp($id, $request->otp);

        if ($response['success'] ?? ($response['status'] ?? false)) {
            // Notification Logic
            $parcel = Parcel::with('merchant.user')->find($id);
            if ($parcel && $parcel->merchant && $parcel->merchant->user) {
                $staff = User::where('user_type', 'staff')->get();
                $merchant = User::where('id', $parcel->merchant->user->id)->get();
                $users = $staff->merge($merchant);
                $title = 'Return Task Completed';
                $details = 'Your parcel (ID: ' . $parcel->parcel_no . ') has been successfully returned to you by our rider.';
                $this->sendNotification($title, $users, $details, ['parcel_read'], 'info', url('merchant/parcel/details/' . $parcel->id), '');
            }

            Toastr::success($response['message']);
        } else {
            Toastr::error($response['message']);
        }

        return redirect()->back();
    }
}
