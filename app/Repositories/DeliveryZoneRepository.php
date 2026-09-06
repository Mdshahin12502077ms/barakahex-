<?php

namespace App\Repositories;

use App\Models\DeliveryZone;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class DeliveryZoneRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(DeliveryZone $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return DeliveryZone::with('thana')->get();
    }

    public function activeDeliveryZones()
    {
        return DeliveryZone::active()->get();
    }

    public function store($request)
    {
        return DeliveryZone::create($request);
    }

    public function find($id)
    {
        return DeliveryZone::find($id);
    }

    public function update($request, $id)
    {
        return DeliveryZone::find($id)->update($request);
    }

    public function delete($id): int
    {
        return DeliveryZone::destroy($id);
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
