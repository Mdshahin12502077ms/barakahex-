<?php

namespace App\Repositories;

use App\Models\Division;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class DivisionRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Division $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Division::with('country')->get();
    }

    public function activeDivisions()
    {
        return Division::active()->get();
    }

    public function store($request)
    {
        return Division::create($request);
    }

    public function find($id)
    {
        return Division::find($id);
    }

    public function update($request, $id)
    {
        return Division::find($id)->update($request);
    }

    public function delete($id): int
    {
        return Division::destroy($id);
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
