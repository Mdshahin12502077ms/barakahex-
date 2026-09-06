<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\Branch;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Repositories\Interfaces\BranchInterface;

class BranchRepository implements BranchInterface
{

    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }

    public function paginate()
    {
        return Branch::orderByDesc('id')->paginate(\Config::get('parcel.paginate'));
    }

    public function allUsers()
    {
        return User::get();
    }

    public function get($id)
    {
        return Branch::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $branch = new Branch();
            $branch->user_id = $request->manager;
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->phone_number = $request->phone_number;
            $branch->parent_id = $request->parent_id;
            $branch->type = $request->type;
            $branch->save();

            $user = $branch->user;
            $user->branch_id = $branch->id;
            $user->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($request)
    {

        DB::beginTransaction();
        try {
            $branch = $this->get($request->id);
            $branch->user_id = $request->manager;
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->type= $request->type;
            $branch->parent_id= $request->parent_id;
            $branch->phone_number = $request->phone_number;
            $branch->save();

            $user = $branch->user;
            $user->branch_id = $branch->id;
            $user->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {

            $branch = $this->get($id);

            $branch->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    public function statusChange($request)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->findOrFail($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();
            DB::commit();
            return $this->responseWithSuccess(__('updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }

    public function changeDefault($request)
    {
        try {
            $branch = Branch::find($request['branch_id']);
            $old_default = Branch::where('default', 1)->first();
            if (!blank($old_default)):
                $old_default->default = 0;
                $old_default->save();
            endif;
            $branch->default = 1;
            $branch->save();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $e) {
            return $this->responseWithError($e->getMessage(), []);
        }
    }
}
