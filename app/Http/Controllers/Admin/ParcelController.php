<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\User;
use App\Models\Branch;
use App\Models\Charge;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\CodCharge;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Http\Request;
use App\Exports\ClosingReport;
use App\Traits\SmsSenderTrait;
use Illuminate\Support\Carbon;
use App\Exports\FilteredParcel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\Admin\ParcelDataTable;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Repositories\Interfaces\ParcelInterface;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Http\Requests\Admin\Parcel\ParcelStoreRequest;
use App\Http\Requests\Admin\Parcel\ParcelUpdateRequest;
use App\Http\Requests\Admin\Parcel\PartialDeliveryRequest;
use App\Http\Requests\Admin\Parcel\TransferToBranchRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Image;


class ParcelController extends Controller
{
    use SmsSenderTrait;
    protected $parcels;
    protected $delivery_man;

    public function __construct(ParcelInterface $parcels, DeliveryManInterface $delivery_man)
    {
        $this->parcels = $parcels;
        $this->delivery_man = $delivery_man;
    }

    public function index(Request $request, ParcelDataTable $dataTable)
    {
        $data['title'] = __('parcel');
        $countData = $dataTable->getTotalCount();
        $data['countData'] = $countData;
        $data['charges'] = Charge::all();
        $data['cod_charges'] = CodCharge::all();
        $data['branchs'] = Branch::all();
        $data['third_parties'] = ThirdParty::where('status', true)->orderBy('name')->get();
        return $dataTable->render('admin.parcel.index', $data);
    }
    public function create()
    {
        if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff):
            $charges = Charge::all();
            $cod_charges = CodCharge::all();
            $branchs = Branch::all();
            $districts = District::with(['thanas' => function($q) { $q->where('status', 'active')->orderBy('name'); }])->active()->orderBy('name')->get();
            $city = $districts;
            $branch = Branch::all();
            return view('admin.parcel.create', compact('charges', 'cod_charges', 'branchs', 'districts', 'city', 'branch'));
        else:
            return back()->with('danger', __('service_unavailable'));
        endif;
    }

    public function thanabook(Request $request)
    {
        $district_id = $request->district_id ?? $request->city_id;
        $thanas = Thana::where('district_id', $district_id)->where('status', 'active')->orderBy('name')->get();

        if ($request->ajax() && $request->wantsJson()) {
            return response()->json($thanas);
        }

        $output = "<option value=''>Select Thana</option>";
        foreach ($thanas as $thana) {
            $output .= "<option value='" . $thana->id . "'>" . $thana->name . "</option>";
        }
        return response($output);
    }
    public function store(ParcelStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff):
                if ($this->parcels->store($request)):
                    return redirect()->back()->with('success', __('created_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
                endif;
            else:
                return redirect()->route('parcel')->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Store Error: ' . $e);
            return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
        }
    }
    public function edit($id)
    {
        $parcel = $this->parcels->get($id);

        if (
            hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
            || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
        ):

            $charges = Charge::all();
            $branchs = Branch::all();
            if (
                $parcel->status == 'pending'
                || $parcel->status == 'pickup-assigned'
                || $parcel->status == 're-schedule-pickup'
                || $parcel->status == 'received-by-pickup-man'
                || $parcel->status == "received"
                || $parcel->status == "transferred-to-branch"
                || $parcel->status == "delivery-assigned"
                || $parcel->status == "re-schedule-delivery"
                || ($parcel->status == "returned-to-warehouse" && $parcel->is_partially_delivered == false)
                || ($parcel->status == "return-assigned-to-merchant" && $parcel->is_partially_delivered == false)
            ):

                return view('admin.parcel.edit', compact('parcel', 'charges', 'branchs'));
            else:
                return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
            endif;
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function update(ParcelUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->update($request)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;

            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelDelete(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($parcel->status == 'deleted'):
                    return back()->with('danger', __('this_parcel_has_already_been_deleted'));
                endif;

                if ($this->parcels->parcelDelete($request)):
                    return redirect()->route('parcel')->with('success', __('deleted_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function assignPickupMan(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->assignPickupMan($request)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function assignDeliveryMan(Request $request)
    {
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (!$parcel)
                    dd($parcel);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($this->parcels->assignDeliveryMan($request, $id, 'normal')):
                        $message = __('delivery_man_assigned_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reSchedulePickup(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $data = $this->parcels->reSchedulePickup($request);
            return response()->json($data);
        } catch (\Exception $e) {
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function parcelCod(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            $data[1] = $parcel->price;

            return response()->json($data);
        } catch (\Exception $e) {
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function reSchedulePickupMan(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->parcels->reSchedulePickupMan($request)):
                return redirect()->route('parcel')->with('success', __('pickup_rescheduled_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reScheduleDelivery(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $data = $this->parcels->reScheduleDelivery($request);
            return response()->json($data);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reScheduleDeliveryMan(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->parcels->reScheduleDeliveryMan($request)):
                return redirect()->route('parcel')->with('success', __('delivery_rescheduled_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function returnAssignToMerchant(Request $request)
    {

        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);

                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($this->parcels->returnAssignToMerchant($request, $id)):
                        $message = __('return_assign_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelCancel(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->status == 'cancel'):
                    return back()->with('danger', __('this_parcel_has_already_been_cancelled'));
                endif;

                if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified' || $parcel->status == 'returned-to-merchant' || $parcel->status == 'partially-delivered'):
                    return back()->with('danger', __('this_parcel_can_not_be_cancelled'));
                endif;

                if ($this->parcels->parcelCancel($request)):
                    return redirect()->route('parcel')->with('success', __('cancelled_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReceiveByPickupman(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($parcel->status == 'received-by-pickup-man'):
                        $error = 1;
                        $message = __('this_parcel_has_already_been_received_by_pickup_man');
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'received-by-pickup-man', $request->note)):
                        $message = __('updated_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReceive(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($parcel->status != 'received'):
                        if ($this->parcels->parcelStatusUpdate($parcel->id, 'received', $request->note, $request->branch)):
                            $message = __('updated_successfully');
                        else:
                            $error = 1;
                            $message = __('something_went_wrong_please_try_again_later');
                        endif;
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelDelivery(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'partially-delivered' || $parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_delivered'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'delivered', $request->note)):
                        $message = __('updated_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function partialDelivery(PartialDeliveryRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->status == 'partially-delivered' || $parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                    return back()->with('danger', __('this_parcel_has_already_confirmed_as_delivered'));
                endif;

                if ($this->parcels->partialDelivery($request)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReturnToGreenx(Request $request)
    {
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'returned-to-warehouse'):
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_returned_to_warehouse'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'returned-to-warehouse', $request->note)):
                        $message = __('updated_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function returnToMerchant(Request $request)
    {
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'returned-to-merchant'):
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_returned_to_merchant'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'returned-to-merchant', $request->note)):
                        $message = __('updated_successfully');
                    else:
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reverseFromCancel(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->reverseUpdate($parcel->id, $parcel->status_before_cancel, $request->note)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function transferTobranch(TransferToBranchRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->branch_id == $request->branch):
                    return back()->with('danger', __('branch_must_be_different'));
                endif;

                if ($parcel->status == 'transferred-to-branch'):
                    return back()->with('danger', __('this_parcel_has_already_assigned_for_transfer_to_branch'));
                endif;

                if ($this->parcels->parcelStatusUpdate($parcel->id, 'transferred-to-branch', $request->note, $request->branch, $request->delivery_man)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function transferReceiveTobranch(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($parcel->status == 'transferred-received-by-branch'):
                    return back()->with('danger', __('this_parcel_has_already_assigned_for_transfer_to_branch'));
                endif;

                if ($this->parcels->parcelStatusUpdate($parcel->id, 'transferred-received-by-branch', $request->note, $parcel->transfer_to_branch_id)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function deliveryReverse(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($this->parcels->deliveryReverse($request)):
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }


    public function filter(Request $request)
    {
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();

        $query = Parcel::query();

        if (!hasPermission('read_all_parcel')) {
            $query->where(function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            });
        }

        if ($request->parcel_no != "") {
            $query->where('parcel_no', $request->parcel_no);
            $parcels = $query->paginate(\Config::get('parcel.paginate'));

            return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
        }

        if ($request->phone_no != "") {
            $query->where('customer_phone_number', 'LIKE', '%' . $request->phone_no);
            $parcels = $query->paginate(\Config::get('parcel.paginate'));

            return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
        }

        if ($request->created_from != "") {
            $created_from = date("Y-m-d", strtotime($request->created_from));
            $query->whereDate('created_at', '>=', "{$created_from}%");
            if ($request->created_to != "") {
                $created_to = date("Y-m-d", strtotime($request->created_to));
                $query->whereDate('created_at', '<=', "{$created_to}%");
            }
        }

        if ($request->pickup_branch != 'all') {
            $query->when($request->pickup_branch == 'pending', function ($search) {
                $search->where('pickup_branch_id', null);
            })->when($request->pickup_branch != 'pending', function ($search) use ($request) {
                $search->where('pickup_branch_id', $request->pickup_branch);
            });
        }

        if ($request->branch != "any") {
            $query->where('branch_id', $request->branch);
        }

        if ($request->merchant != "") {
            $query->where('merchant_id', $request->merchant);
        }
        if ($request->phone_number != "") {
            $query->whereHas('merchant', function ($inner_query) use ($request) {
                $inner_query->where('phone_number', 'LIKE', "%{$request->phone_number}%");
            });
        }

        if ($request->customer_invoice_no != "") {
            $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
        }

        if ($request->status != "any") {
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
        }

        if ($request->pickup_man != "any") {
            $query->where('pickup_man_id', $request->pickup_man);
        }

        if ($request->delivery_man != "any") {
            $query->where('delivery_man_id', $request->delivery_man);
        }

        if ($request->weight != "any") {
            $query->where('weight', $request->weight);
        }

        if ($request->parcel_type != "any") {
            $query->where('parcel_type', $request->parcel_type);
        }

        if ($request->location != "any") {
            $query->where('location', $request->location);
        }

        if ($request->pickup_date != "") {
            $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
            $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
        }

        if ($request->delivery_date != "") {
            $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
            $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
        }

        if ($request->third_party != "any") {
            $query->where('third_party_id', $request->third_party);
        }

        if ($request->delivered_date != "") {
            $delivered_date = date("Y-m-d", strtotime($request->delivered_date));

            $query->where('delivered_date', 'LIKE', "%{$delivered_date}%");

            // $query->whereHas('events', function ($inner_query) use ($delivered_date) {
            //     $inner_query->where('title', 'parcel_delivered_event');
            //     $inner_query->where('created_at', 'LIKE', "%{$delivered_date}%");
            // });
        }
        if ($request->returned_date != "") {
            $returned_date = date("Y-m-d", strtotime($request->returned_date));
            $query->where('returned_date', 'LIKE', "%{$returned_date}%");
        }
        if ($request->has('download')):
            $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
            return Excel::download(new FilteredParcel($query), $file_name);
        endif;

        $parcels = $query->latest()->paginate(\Config::get('parcel.parcel_merchant_paginate'));

        return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
    }

    public function getParcelDownload(Request $request)
    {

        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();

        $query = Parcel::query();
        if (!hasPermission('read_all_parcel')) {
            $query->where(function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            });
        }
        if ($request->merchant_id) {
            $query->where('merchant_id', $request->merchant_id);
        }
        if ($request->phone_number) {
            $query->whereHas('merchant', function ($inner_query) use ($request) {
                $inner_query->where('phone_number', 'LIKE', "%{$request->phone_number}%");
            });
        }
        if ($request->customer_invoice_no) {
            $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
        }
        if ($request->created_at != "") {
            $query->when($request->created_at ?? false, function ($query, $created_at) {
                $dateRange = $this->parseDate($created_at);
                $query->whereBetween('created_at', $dateRange);
            });
        }
        if ($request->pickup_date) {
            $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
            $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
        }
        if ($request->delivery_date) {
            $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
            $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
        }
        if ($request->delivered_date) {
            $delivered_date = date("Y-m-d", strtotime($request->delivered_date));
            $query->where('delivered_date', 'LIKE', "%{$delivered_date}%");

            // $query->whereHas('events', function ($inner_query) use ($delivered_date) {
            //     $inner_query->where('title', 'parcel_delivered_event');
            //     $inner_query->where('created_at', 'LIKE', "%{$delivered_date}%");
            // });
        }
        if ($request->returned_date != "") {
            $returned_date = date("Y-m-d", strtotime($request->returned_date));
            $query->where('returned_date', 'LIKE', "%{$returned_date}%");
        }
        if ($request->pickup_man_id) {
            $query->where('pickup_man_id', $request->pickup_man_id);
        }
        if ($request->delivery_man_id) {
            $query->where('delivery_man_id', $request->delivery_man_id);
        }
        if ($request->third_party_id) {
            $query->where('third_party_id', $request->third_party_id);
        }
        if ($request->status) {
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
        }
        if ($request->weight) {
            $query->where('weight', $request->weight);
        }
        if ($request->parcel_type) {
            $query->where('parcel_type', $request->parcel_type);
        }
        if ($request->location) {
            $query->where('location', $request->location);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->pickup_branch_id) {
            $query->when($request->pickup_branch_id == 'pending', function ($search) {
                $search->where('pickup_branch_id', null);
            })->when($request->pickup_branch_id != 'pending', function ($search) use ($request) {
                $search->where('pickup_branch_id', $request->pickup_branch_id);
            });
        }
        $filename = 'filtered_parcels_' . now()->format('YmdHis') . '.xlsx';
        return Excel::download(new FilteredParcel($query), $filename);
        // return Excel::download(new FilteredParcel($query), $filename);

    }

    public function shops(Request $request)
    {
        $requested_merchant = Merchant::findOrfail($request->merchant_id);
        if (isset($request->type)) {
            $warehouses = $requested_merchant->warehouse;
            return view('admin.parcel.warehouse', compact('warehouses'))->render();
        }
        $shops = $requested_merchant->shops;
        return view('admin.parcel.shops', compact('shops'))->render();
    }

    public function shop(Request $request)
    {
        $shop = Shop::find($request->shop_id);
        $data['shop_pickup_branch'] = $shop->branch->name ?? '';
        $data['shop_phone_number'] = $shop->shop_phone_number;
        $data['address'] = $shop->address;
        return response()->json($data);
    }

    public function warehousesProduct(Request $request)
    {
        $warehouses_stock = Stock::where('warehouse_id', $request->warehouse_id)->get();
        return view('admin.parcel.product', compact('warehouses_stock'));
    }

    public function default(Request $request)
    {
        $default_shop = Shop::where('merchant_id', $request->merchant_id)->where('default', 1)->first();
        $pickup_branch = Merchant::find($request->merchant_id)->user->branch_id;

        $data['shop_phone_number'] = $default_shop->shop_phone_number;
        $data['address'] = $default_shop->address;

        $branchs = Branch::all();

        $options = view('admin.parcel.branchs', compact('branchs', 'pickup_branch'))->render();

        $data['pickup_branch'] = $options;
        return response()->json($data);
    }

    public function merchantStaff(Request $request)
    {
        $staffs = Merchant::find($request->merchant_id)->staffs;

        return view('admin.parcel.staffs', compact('staffs'))->render();
    }


    public function detail($id)
    {
        try {
            $parcel = Parcel::with([
                'merchant.user',
                'events',
                'branch',
                'movements.fromBranch',
                'movements.toBranch',
                'movements.sender',
                'movements.receiver',
                'movements.deliveryMan.user',
                'destinationBranch'
            ])->find($id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('admin.parcel.detail', compact('parcel', 'cod_charges', 'charges'));
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function print($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::find($id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                $delivery_men = $this->delivery_man->all();
                return view('merchant.parcel.print', compact('parcel', 'cod_charges', 'charges', 'delivery_men'));
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function duplicate($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff):
                $parcel = $this->parcels->get($id);
                $charges = Charge::all();
                $branchs = Branch::all();

                return view('admin.parcel.create', compact('parcel', 'charges', 'branchs'));
            else:
                return back()->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function sticker($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {

            $parcel = Parcel::with('branch', 'pickupBranch')->find($id);
            $qr_code = QrCode::size(50)
                ->generate($parcel->parcel_no);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if (settingHelper('label_sticker') == 'cCorrier') {
                    return view('admin.parcel.e_courier', compact('parcel', 'qr_code'));
                } elseif (settingHelper('label_sticker') == 'pathao') {
                    return view('admin.parcel.pathao', compact('parcel', 'qr_code'));
                } else {
                    return view('admin.parcel.sticker', compact('parcel', 'qr_code'));
                }

            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function notifyPickupMan($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::find($id);

            $sms_body = $parcel->pickupMan->user->first_name . ', a pickup has been assigned to you. Address: ' . $parcel->pickup_address . ', Phone number: ' . $parcel->pickup_shop_phone_number . ', Pickup date: ' . $parcel->pickup_date;

            if ($this->test($sms_body, $parcel->pickupMan->phone_number, 'notify_pickup_man', setting('active_sms_provider'))):
                return back()->with('success', __('notified_successfully'));
            else:
                return back()->with('danger', __('unable_to_notify'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reverseOptions(Request $request)
    {

        $status = $this->parcels->get($request->id)->status;
        $is_partially_delivered = $this->parcels->get($request->id)->is_partially_delivered;

        return view('admin.parcel.reverse-options', compact('status', 'is_partially_delivered'))->render();
    }

    public function transferOptions(Request $request)
    {
        $current_branch = $this->parcels->get($request->id)->branch_id;
        $branchs = Branch::where('id', '!=', $current_branch)->orderBy('type')->orderBy('name')->get();

        return view('admin.parcel.transfer-options', compact('branchs'))->render();
    }

    public function reverseUpdate($id, $status, $note = '')
    {
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try {
            if ($this->parcels->reverseUpdate($id, $status)):
                $success[0] = __('updated_successfully');
                $success[1] = 'success';
                $success[2] = __('updated');
                return response()->json($success);
            endif;
        } catch (\Exception $e) {
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function parcelFiltering($slug)
    {
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();
        $parcels = Parcel::when($slug == 'pending-return', function ($q) {
            $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
        })
            ->when($slug == 'partially-delivered', function ($q) {
                $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                    ->where('is_partially_delivered', '=', 1);
            })
            ->when($slug != 'pending-return' && $slug != 'partially-delivered', function ($q) use ($slug) {
                $q->where('status', $slug);
            })
            ->when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where(function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                        ->orWhereNull('pickup_branch_id')
                        ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                });
            })
            ->orderByDesc('id')
            ->paginate(\Config::get('parcel.parcel_merchant_paginate'));
        return view('admin.parcel.index', compact('parcels', 'charges', 'cod_charges', 'slug', 'branchs', 'third_parties'));
    }

    public function chargeDetails(Request $request)
    {
        $data = $this->parcels->chargeDetails($request);
        return response()->json($data);
    }

    public function customerDetails(Request $request)
    {
        $data = $this->parcels->customerDetails($request);

        return response()->json($data);
    }

    public function location(Request $request)
    {
        $data['location'] = $this->parcels->get($request->id)->location;

        return response()->json($data);
    }

    public function download($id)
    {
        $merchant = Merchant::find($id);
        $file_name = $merchant->company . ' ' . '- Closing ' . date('Y-m-d') . '.xlsx';
        return Excel::download(new ClosingReport($id), $file_name);
    }


    public function trackParcel($id)
    {
        return $this->parcels->trackParcel($id);
    }

    private function parseDate($date_range)
    {
        $dates = explode('to', $date_range);

        if (count($dates) == 1) {
            $dates[1] = $dates[0];
        }

        $start_date = trim($dates[0]);
        $end_date = trim($dates[1]);

        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';

        return [
            Carbon::parse($start_date)->format('Y-m-d H:s:i'),
            Carbon::parse($end_date)->format('Y-m-d H:s:i'),
        ];
    }
    public function batch_print(Request $request)
    {
        $parcelIds = $request->get('parcel_ids'); // This will be a comma-separated string

        $parcelIdsArray = explode(',', $parcelIds);

        $parcels = Parcel::whereIn('id', $parcelIdsArray)->get();

        $qrCodes = $parcels->map(function ($parcel) {
            return QrCode::size(50)->generate($parcel->parcel_no);
        });

        return view('admin.exports.sticker_download', compact('parcels', 'qrCodes'));
    }
    public function checkReceived(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // how many of the selected parcels are *not* yet “received”?
        $notReceived = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'received')
            ->count();

        return response()->json([
            'all_received' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkDeliveryAssigned(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // Step 1: Check parcels that are not yet delivery-assigned
        $notAssignedCount = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'delivery-assigned')
            ->count();


        return response()->json([
            'all_valid' => $notAssignedCount === 0,
            'not_assigned_count' => $notAssignedCount,
        ]);
    }

    public function checkAssignedPickupman(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // how many of the selected parcels are *not* yet “pickup-assigned”?
        $notAssigned = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'pickup-assigned')
            ->count();

        return response()->json([
            'all_received' => $notAssigned === 0,
            'invalid_count' => $notAssigned,
        ]);
    }
    public function checkPickedUp(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // how many of the selected parcels are *not* yet “received by pickup man”?
        $notReceived = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'received-by-pickup-man')
            ->count();

        return response()->json([
            'all_received' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkReturnToWarehouse(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // how many of the selected parcels are *not* yet “received by pickup man”?
        $notReceived = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'returned-to-warehouse')
            ->count();

        return response()->json([
            'all_valid' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkReturnAssignToMerchant(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        // how many of the selected parcels are *not* yet “received by pickup man”?
        $notReceived = Parcel::whereIn('id', $ids)
            ->where('status', '!=', 'return-assigned-to-merchant')
            ->count();

        return response()->json([
            'all_valid' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }
    public function export_parcel(Request $request)
    {
        $parcelIdsArray = explode(',', $request->parcel_ids);

        $query = Parcel::query();

        $query = $query->whereIn('id', $parcelIdsArray);

        $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
        return Excel::download(new FilteredParcel(Parcel::with(['destination', 'type'])->whereIn('id', $parcelIdsArray)->latest()->limit(8000)->get()), $file_name);
    }
}
