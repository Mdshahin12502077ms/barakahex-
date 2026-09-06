<?php

namespace App\Http\Controllers\Api\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Resources\Api\BankResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\MerchantPaymentAccount;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\WithdrawInterface;
class NotificationController extends Controller
{
    use ApiReturnFormatTrait;
    protected $withdrawRepo;

    public function __construct(WithdrawInterface $withdrawRepo)
    {
        $this->withdrawRepo     = $withdrawRepo;
    }
    public function notification(Request $request)
    {
        try {
            $user = jwtUser();

            if (!$user) {
                return $this->responseWithError('User not authenticated');
            }

            $notifications = Notification::select('notifications.*','nu.id as notification_user_id')
                                ->join('notification_users as nu','nu.notification_id','=','notifications.id')
                                ->where('nu.user_id', $user->id)->where('nu.is_read',0)
                                ->groupBy('nu.notification_id')
                                ->latest()->limit(5)->get();

            $notificationCount = NotificationUser::where('user_id', $user->id)->where('is_read', 0)->count();


            $data = [
                'notifications'           => $notifications,
                'notificationCount'       => $notificationCount,
            ];

            return $this->responseWithSuccess('Notification info retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }

    }



    public function updateNotification($id)
    {
        $user                      = jwtUser();

        try {
            $notification           = NotificationUser::find($id);
            $notification->is_read  = 1;
            $notification->save();
            $data                   = Notification::where('id', $notification->notification_id)->first();
            $url 					= $data->url;
            $parts 					= explode('/', parse_url($url, PHP_URL_PATH));
            $invoiceId 				= end($parts);
            $data = [
                'id'           => $invoiceId,
            ];

            return $this->responseWithSuccess('Notification info retrieved successfully', [], $data);

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
