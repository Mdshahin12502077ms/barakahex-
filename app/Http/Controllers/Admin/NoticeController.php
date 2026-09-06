<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\NoticeInterface;
use Illuminate\Http\Request;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use App\DataTables\Admin\NoticeDataTable;

class NoticeController extends Controller
{
    use RepoResponseTrait, ApiReturnFormatTrait;
    protected $notices;

    public function __construct(NoticeInterface $notices)
    {
        $this->notices  = $notices;
    }

    public function index(NoticeDataTable $dataTable, Request $request)
    {
        $notices        = $this->notices->paginate(10);

        return $dataTable->render('admin.notice.index', compact('notices'));

    }

    public function create()
    {
        return view('admin.notice.create');
    }

    public function store(Request $request)
    {
        if($this->notices->store($request)):
            return redirect()->route('notice')->with('success', __('created_successfully'));
        else:
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        endif;
    }

    public function edit($id)
    {
        $notice = $this->notices->get($id);
        if (!blank($notice)):
            return view('admin.notice.edit', compact('notice'));
        else:
            return back()->with('danger', __('not_found'));
        endif;
    }

    public function update(Request $request)
    {
        if($this->notices->update($request)):
            return redirect()->route('notice')->with('success', __('updated_successfully'));
        else:
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        endif;
    }

    public function statusChange(Request $request)
    {

        $status = $this->notices->statusChange($request);
        if($status == true){
            $success = __('updated_successfully');
            return response()->json([
                'status'=>200,
                'message'=>$success,
            ]);
        }else{
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success,404);
        }
    }

    public function delete($id)
    {
        if($this->notices->delete($id)):
            $success[0] = __('deleted_successfully');
            $success[1] = 'success';
            $success[2] = __('deleted');
            return response()->json($success);
        else:
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        endif;
    }
}
