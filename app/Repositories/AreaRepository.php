<?php

namespace App\Repositories;

use App\Models\Area;
use App\Models\DeliveryZone;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class AreaRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Area $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Area::with(['deliveryZone', 'thana', 'district', 'division', 'country'])->get();
    }

    public function activeAreas()
    {
        return Area::active()->get();
    }

    public function store($request)
    {
        if (isset($request['delivery_zone_id'])) {
            $zone = DeliveryZone::with('thana.district.division.country')->find($request['delivery_zone_id']);
            if ($zone) {
                $request['thana_id']    = $request['thana_id'] ?? $zone->thana_id;
                $request['district_id'] = $request['district_id'] ?? ($zone->thana->district_id ?? null);
                $request['division_id'] = $request['division_id'] ?? ($zone->thana->district->division_id ?? null);
                $request['country_id']  = $request['country_id'] ?? ($zone->thana->district->division->country_id ?? null);
            }
        }
        $request['created_by'] = auth()->id() ?? 1;

        return Area::create($request);
    }

    public function find($id)
    {
        return Area::with(['deliveryZone', 'thana', 'district', 'division', 'country'])->find($id);
    }

    public function update($request, $id)
    {
        if (isset($request['delivery_zone_id'])) {
            $zone = DeliveryZone::with('thana.district.division.country')->find($request['delivery_zone_id']);
            if ($zone) {
                $request['thana_id']    = $request['thana_id'] ?? $zone->thana_id;
                $request['district_id'] = $request['district_id'] ?? ($zone->thana->district_id ?? null);
                $request['division_id'] = $request['division_id'] ?? ($zone->thana->district->division_id ?? null);
                $request['country_id']  = $request['country_id'] ?? ($zone->thana->district->division->country_id ?? null);
            }
        }
        $request['updated_by'] = auth()->id() ?? 1;

        return Area::find($id)->update($request);
    }

    public function delete($id): int
    {
        return Area::destroy($id);
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
