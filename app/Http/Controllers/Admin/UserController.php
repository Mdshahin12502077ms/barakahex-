<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Role;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use App\Traits\RepoResponseTrait;
use App\Http\Controllers\Controller;
use App\Traits\ApiReturnFormatTrait;
use App\Models\Account\CompanyAccount;
use App\DataTables\Admin\StaffDataTable;
use App\Repositories\Interfaces\UserInterface;
use App\Http\Requests\Admin\Users\UserStoreRequest;
use App\Repositories\Interfaces\Role\RoleInterface;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Repositories\Interfaces\PermissionInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Brian2694\Toastr\Facades\Toastr;
use App\DataTables\Admin\PayoutLogDataTable;
use App\Models\User;

class UserController extends Controller
{
    use ApiReturnFormatTrait, RepoResponseTrait;
    protected $users;
    protected $roles;
    protected $permissions;

    public function __construct(UserInterface $users, RoleInterface $roles, PermissionInterface $permissions)
    {
        $this->users           = $users;
        $this->roles           = $roles;
        $this->permissions     = $permissions;
    }

    public function index(StaffDataTable $dataTable, Request $request)
    {
        $data['total_user'] = $dataTable->getTotalCount();
        return $dataTable->with($request->all())->render('admin.users.index', $data);
    }

    public function create()
    {
        $roles = Role::where('id', '!=', 1)->where('status', 'active')->get();
        $branchs  = Branch::active()->get();
        $permissions = $this->permissions->all();
        return view('admin.users.create', compact('roles', 'permissions', 'branchs'));
    }

    public function changeRole(Request $request)
    {
        $role_permissions = $this->roles->get($request->role_id)->permissions;
        $permissions = $this->permissions->all();
        return view('admin.users.permissions', compact('permissions', 'role_permissions'))->render();
    }

    public function store(UserStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {

            if ($this->users->store($request)):
                return redirect()->route('users')->with('success', __('created_successfully'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        if ($id == '1'):
            return back()->with('danger', __('access_denied'));
        endif;
        $user               = User::find($id)->load(['branch', 'roleUser']);
        
        $roles              = Role::where('id', '!=', 1)->where('status', 'active')->get();
        $branchs            = Branch::active()->get();
        $role_permissions   = $user->permissions;
        $permissions        = $this->permissions->all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'role_permissions', 'branchs'));
    }

    public function update(UserUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }

        try {

            if ($request->id == '1'):
                return back()->with('danger', __('access_denied'));
            endif;
            $branch = Branch::where('user_id', $request->id)->first();

            if (isset($branch) && $request->branch != $branch->id):
                return back()->with('danger', __('this_user_is_in_charge_of_another_branch'));
            endif;

            if ($this->users->update($request)):
                return redirect()->route('users')->with('success', __('updated_successfully'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function delete($id)
    {
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try {
            if ($this->users->delete($id)):
                $success[0] = __('deleted_successfully');
                $success[1] = 'success';
                $success[2] = __('deleted');
                return response()->json($success);
            endif;
        } catch (\Exception $e) {
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 404,
                'message' => $message,
            ]);
        }
        try {
            $status = $this->users->statusChange($request);

            if ($status == true) {
                $success = __('updated_successfully');
                return response()->json([
                    'status' => 200,
                    'message' => $success,
                ]);
            }
        } catch (\Exception $e) {
            $message = __('something_went_wrong_please_try_again');
            return response()->json([
                'status' => 404,
                'message' => $message,
            ]);
        }
    }

    public function personalInfo($id)
    {
        $user = $this->users->get($id);
        return view('admin.users.details.personal-info', compact('user'));
    }

    public function accountActivity($id)
    {
        $login_activities = LogActivity::where('user_id', $id)->orderBy('id', 'desc')->paginate(1);
        $user = $this->users->get($id);
        return view('admin.users.details.account-activity', compact('login_activities', 'user'));
    }

    public function paymentLogs($id, PayoutLogDataTable $dataTable)
    {
        $statements = CompanyAccount::orderby('id', 'desc')->where('user_id', $id)->orderBy('id', 'desc')->paginate(1);
        $user = $this->users->get($id);

        return $dataTable->render('admin.users.details.payment-logs.payment-logs', compact('statements', 'user'));
    }

    public function staffAccounts($id)
    {
        $user = $this->users->get($id);
        $accounts = $user->accounts($id);
        return view('admin.users.details.accounts', compact('user', 'accounts'));
    }

    public function logoutUserDevices($id)
    {
        $user = Sentinel::findById($id);

        if (Sentinel::logout($user, true)):
            $success[0] = __('logout_successfully');
            $success[1] = 'success';
            $success[2] = __('logout');
            return response()->json($success);
        else:
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        endif;
    }
}
