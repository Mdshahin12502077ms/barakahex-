<?php

namespace App\Repositories\Admin;

use App\Models\PercelOtpLog;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;

class OtpLogRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(PercelOtpLog $model)
    {
        $this->model = $model;
    }

    public function allData($paginate = 15)
    {
        return $this->model->latest('id')->with(['user', 'parcel', 'deliveryMan.user'])->paginate($paginate);
    }

    public function find($id)
    {
        return $this->model->with(['user', 'parcel', 'deliveryMan.user'])->find($id);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $log = $this->model->findOrFail($id);
            $log->delete();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }
}
