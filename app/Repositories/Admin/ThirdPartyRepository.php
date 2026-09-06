<?php


namespace App\Repositories\Admin;

use App\Enums\StatusEnum;
use App\Models\ThirdParty;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Repositories\Interfaces\Admin\ThirdPartyInterface;

class ThirdPartyRepository implements ThirdPartyInterface
{
    use ApiReturnFormatTrait,RepoResponseTrait;

    public function paginate()
    {
        return ThirdParty::orderByDesc('id')->paginate(\Config::get('parcel.paginate'));
    }

    public function get($id)
    {
        return ThirdParty::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $third_party                = new ThirdParty();
            $third_party->name          = $request->name;
            $third_party->address       = $request->address;
            $third_party->phone_number  = $request->phone_number;
            $third_party->save();

            DB::commit();
            return true;
        } catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $third_party                = $this->get($request->id);
            $third_party->name          = $request->name;
            $third_party->address       = $request->address;
            $third_party->phone_number  = $request->phone_number;
            $third_party->save();

            DB::commit();
            return true;
        } catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }


    public function changeStatus($request)
    {
        try {
            DB::beginTransaction();
            $row                = $this->get($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status    = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status    = StatusEnum::ACTIVE;
            }
            $row->save();
            DB::commit();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $third_party = $this->get($id);

            $third_party->delete();

            DB::commit();

            return true;
        }   catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }
}
