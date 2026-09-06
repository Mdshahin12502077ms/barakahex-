<?php

namespace App\Repositories;

use App\Models\Thana;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class ThanaUpazilaRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Thana $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Thana::with('district')->get();
    }

    public function activeUpazilas()
    {
        return Thana::active()->get();
    }

    public function store($request)
    {
        return Thana::create($request);
    }

    public function find($id)
    {
        return Thana::find($id);
    }

    public function update($request, $id)
    {
        return Thana::find($id)->update($request);
    }

    public function delete($id): int
    {
        return Thana::destroy($id);
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
