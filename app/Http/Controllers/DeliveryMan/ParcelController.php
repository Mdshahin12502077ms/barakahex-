<?php

namespace App\Http\Controllers\DeliveryMan;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Repositories\DeliveryMan\MyPickupRepository;
use App\Repositories\DeliveryMan\MyDeliveryRepository;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class ParcelController extends Controller
{
    protected $pickupRepo;
    protected $deliveryRepo;

    public function __construct(MyPickupRepository $pickupRepo, MyDeliveryRepository $deliveryRepo)
    {
        $this->pickupRepo = $pickupRepo;
        $this->deliveryRepo = $deliveryRepo;
    }

    private function getDeliveryMan()
    {
        $user = Sentinel::getUser();
        return $user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first();
    }

    // ==================== PICKUP METHODS ====================

    public function pickups(Request $request)
    {
        $pickups = $this->pickupRepo->all($request);
        $statistics = $this->pickupRepo->statistics();

        return view('deliveryman.pickups.index', compact('pickups', 'statistics'));
    }

    public function pickupReceived(Request $request, $id)
    {
        try {
            $this->pickupRepo->pickupReceived($id, $request);
            Toastr::success(__('pickup_received_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Pickup Received Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function reschedulePickup(Request $request, $id)
    {
        try {
            $this->pickupRepo->reschedulePickup($id, $request);
            Toastr::success(__('rescheduled_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Reschedule Pickup Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function cancelPickup(Request $request, $id)
    {
        try {
            $this->pickupRepo->cancelPickup($id, $request);
            Toastr::success(__('cancelled_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Cancel Pickup Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    // ==================== DELIVERY METHODS ====================

    public function deliveries(Request $request)
    {
        $deliveries = $this->deliveryRepo->all($request);
        $statistics = $this->deliveryRepo->statistics();

        return view('deliveryman.deliveries.index', compact('deliveries', 'statistics'));
    }

    public function parcelDetail($id)
    {
        try {
            $parcel = $this->deliveryRepo->find($id);
        } catch (\Exception $e) {
            $parcel = Parcel::with(['merchant.user', 'shop', 'events.user', 'events.pickupPerson.user', 'events.deliveryPerson.user'])->findOrFail($id);
        }

        return view('deliveryman.deliveries.detail', compact('parcel'));
    }

    public function delivered(Request $request, $id)
    {
        try {
            $this->deliveryRepo->delivered($id, $request);
            Toastr::success(__('delivered_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Delivered Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function verifyOtp(Request $request, $id)
    {
        try {
            $res = $this->deliveryRepo->verifyOtp($id, $request->otp);
            if (is_array($res)) {
                if ($res['status']) {
                    Toastr::success($res['message']);
                } else {
                    Toastr::error($res['message']);
                }
                return back();
            } elseif ($res) {
                Toastr::success(__('delivery_successfully_verified'));
                return back();
            } else {
                Toastr::error(__('please_provide_correct_otp'));
                return back();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Verify OTP Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function resendOtp($id)
    {
        try {
            $res = $this->deliveryRepo->resendOtp($id);
            if ($res) {
                Toastr::success(__('otp_resend_successfully'));
            } else {
                Toastr::error(__('something_went_wrong_please_try_again'));
            }
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Resend OTP Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function reschedule(Request $request, $id)
    {
        try {
            $this->deliveryRepo->rescheduleDelivery($id, $request);
            Toastr::success(__('rescheduled_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Reschedule Delivery Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $this->deliveryRepo->cancelDelivery($id, $request);
            Toastr::success(__('cancelled_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DeliveryMan Cancel Delivery Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }
}
