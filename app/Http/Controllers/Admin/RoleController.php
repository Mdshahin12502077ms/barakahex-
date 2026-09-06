<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Admin\RoleDataTable;
use App\Http\Requests\Admin\Roles\RoleStoreRequest;
use App\Repositories\Interfaces\Role\RoleInterface;
use App\Http\Requests\Admin\Roles\RoleUpdateRequest;
use App\Repositories\Interfaces\PermissionInterface;
use Brian2694\Toastr\Facades\Toastr;

class RoleController extends Controller
{
    protected $roles;
    protected $permissions;

    public function __construct(RoleInterface $roles, PermissionInterface $permissions)
    {
        $this->roles=$roles;
        $this->permissions=$permissions;

    }

     public function index(RoleDataTable $dataTable, Request $request)
     {
        $data['total_role'] = $dataTable->getTotalCount();
         return $dataTable->with($request->all())->render('admin.roles.index',$data);
     }


    public function create()
    {
        if(!hasPermission('role_create')):
            return view('errors.403');
        endif;
        $permissions = $this->permissions->all();
        return view('admin.roles.create', compact('permissions'));
    }


    public function store(RoleStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if(!hasPermission('role_create')):
                return view('errors.403');
            endif;

            $role = $this->roles->store($request->all());

            return redirect()->route('roles.index')->with('success', __('created_successfully'));
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if(!hasPermission('role_update')):
            return view('errors.403');
        endif;
        $permissions = $this->permissions->all();
        $role = $this->roles->get($id);
        return view('admin.roles.edit', compact('permissions', 'role'));
    }

    public function update(RoleUpdateRequest $request, $id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if(!hasPermission('role_update')):
                return view('errors.403');
            endif;
            $this->roles->update($id, $request->all());
            return redirect()->route('roles.index')->with('success', __('updated_successfully'));
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try{
            if($this->roles->delete($id)):
                $success[0] = __('deleted_successfully');
                $success[1] = 'success';
                $success[2] = __('deleted');
                return response()->json($success);
            endif;
        } catch (\Exception $e){
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
                'status'=>404,
                'message'=>$message,
            ]);
        }
        try {
            $status = $this->roles->statusChange($request);
            if($status == true){
                $success = __('updated_successfully');
                return response()->json([
                    'status'=>200,
                    'message'=>$success,
                ]);
            }

        } catch (\Exception $e){
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message,404);
        }

    }


}

