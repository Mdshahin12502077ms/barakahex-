<?php

namespace App\Repositories;

use App\Models\District;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class DistrictRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(District $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return District::with('division')->get();
    }

    public function activeDistricts()
    {
        return District::active()->get();
    }

    public function store($request)
    {
        return District::create($request);
    }

    public function find($id)
    {
        return District::find($id);
    }

    public function update($request, $id)
    {
        return District::find($id)->update($request);
    }

    public function delete($id): int
    {
        return District::destroy($id);
    }

    public function statusChange($request)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->findOrFail($request->id);
            if ($row->status == StatusEnum::ACTIVE || (is_string($row->status) && $row->status == 'active')) {
                $row->status = StatusEnum::INACTIVE;
            } else {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
