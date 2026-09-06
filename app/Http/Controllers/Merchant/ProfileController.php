<?php
namespace App\Http\Controllers\Merchant;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Shop;
use App\Models\Branch;
use App\Models\User;
use App\Models\Merchant;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserInterface;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Http\Requests\Merchant\MerchantUpdateRequest;
use App\DataTables\Merchant\MerchantPaymentLogDataTable;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use Brian2694\Toastr\Facades\Toastr;
use App\DataTables\Merchant\MerchantShopDataTable;
use App\Traits\RepoResponseTrait;


class ProfileController extends Controller
{
    use RepoResponseTrait;
    protected $users;
    protected $merchants;

    public function __construct(UserInterface $users, MerchantInterface $merchants)
    {
        $this->users           = $users;
        $this->merchants      = $merchants;

    }
    public function profile()
    {

        return view('merchant.profile.staff-profile');
    }
    public function company()
    {
        $merchant = Merchant::where('user_id', Sentinel::getUser()->id)->first();
        return view('merchant.profile.staff-company', compact('merchant'));
    }

    public function securitySetting()
    {
        return view('merchant.profile.staff-security-settings');
    }

    public function notification()
    {
        return view('merchant.profile.staff-notification');
    }

    public function accountActivity()
    {
        $login_activities = LogActivity::where('user_id', Sentinel::getUser()->id)->orderBy('id', 'desc')->paginate(9);
        return view('merchant.profile.staff-account-activity', compact('login_activities'));
    }

    public function changePassword(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            $hasher                 = Sentinel::getHasher();
            $current_password       = $request->current_password;
            $password               = $request->new_password;
            $user                   = Sentinel::getUser();
            if (!$hasher->check($current_password, $user->password)) {
                return back()->with('danger', __('current_password_is_invalid'));
            }

            $user = User::find(Sentinel::getUser()->id);
            $user->password = bcrypt($password);
            $user->last_password_change = date('Y-m-d H:i:s');
            $user->save();
            return back()->with('success', __('updated_successfully'));
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function profileUpdate(UserUpdateRequest $request)
    {

        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            $request->id = Sentinel::getUser()->id;
            if($this->users->updateProfile($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function merchantUpdate(MerchantUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{

            if($this->merchants->updateMerchantByMerchant($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function statements(MerchantPaymentLogDataTable $dataTable, Request $request)
    {
        return $dataTable->render('merchant.profile.statements');
    }

    public function formatLogs($logs){
        $balance = 0;
        foreach ($logs as $log):
            $log['id'] = $log->id;
            $log['source'] = $log->source;
            $log['details'] = $log->details;
            $log['date'] = date('d-M-Y',strtotime($log->date));
            $amount = $log->type == 'income' ? $log->amount : - $log->amount ;
            $balance += $amount;
            $log['amount'] = number_format($amount, 2);
            $log['balance'] = number_format($balance ,2);
            $log['parcel_no'] = @$log->parcel->parcel_no;

            unset($log->company_account_id);
            unset($log->delivery_man_id);
            unset($log->parcel_id);
            unset($log->parcel);
        endforeach;

        return $logs;
    }

    public function charge()
    {
        return view('merchant.profile.charge');
    }

    public function codCharge()
    {
        return view('merchant.profile.cod-charge');
    }

    public function shops(MerchantShopDataTable $dataTable, Request $request)
    {
        $user    = Sentinel::getUser();
        $branchs = Branch::active()->get();

        $shop = $user->user_type == 'merchant' ?
                    $user->merchant->shops()->get() :
                    $user->staffMerchant->shops()->get();

        return $dataTable->render('merchant.profile.shop.shops', compact('shop', 'branchs'));
    }

    public function shopStore(ShopRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->merchants->shopStore($request)):
                return back()->with('success', __('added_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function shopEdit(Request $request){

        $shop       = Shop::find($request->shop_id);
        $branchs    = Branch::active()->get();


        return view('merchant.profile.shop.shop-update', compact('shop', 'branchs'))->render();
    }

    public function shopUpdate(ShopRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->merchants->shopUpdate($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function changeDefault(Request $request)
    {
        if (isDemoMode()) {
            $success  = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>500,
                'message'=>$success,
            ]);
        }
        try{
            $shop = Shop::find($request['shop_id']);
            $old_default = Sentinel::getUser()->merchant->shops()->where('default',1)->first();
            if(!blank($old_default)):
                $old_default->default = 0;
                $old_default->save();
            endif;
            $shop->default = 1;
            $shop->save();
            $success = __('updated_successfully');
            return response()->json([
                'status'=>200,
                'message'=>$success,
            ]);

        }catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success);
        }
    }

    public function shopDelete($id)
    {
        if (isDemoMode()) {
            $success  = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>500,
                'message'=>$success,
            ]);
        }
        try{
            $shop = Shop::find($id);
            $shop->delete();

            $success = __('deleted_successfully');
            return response()->json($success);
        } catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success);
        }
    }

    public function shop(Request $request){
        $shop                       = Shop::with('branch')->find($request->shop_id);
        $data['shop_pickup_branch'] = $shop->branch->name ?? '';
        $data['shop_phone_number']  = $shop->shop_phone_number;
        $data['address']            = $shop->address;
        return response()->json($data);
    }

    public function apiCredentials()
    {
        if (@settingHelper('preferences')->where('title','read_merchant_api')->first()->merchant):
            return view('merchant.profile.api-credentials');
        else:
            return abort(403, 'Access Denied');
        endif;
    }

    public function apiCredentialsUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if ($this->merchants->apiCredentialsUpdate($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
}
