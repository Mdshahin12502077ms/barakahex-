<?php

namespace App\Repositories;

use App\Models\Country;
use App\Enums\StatusEnum;
use App\Traits\CommonHelperTrait;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;

class CountryRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Country::all();
    }

    public function activeCountries()
    {
        return Country::active()->get();
    }

    public function store($request)
    {
        return Country::create($request);
    }

    public function find($id)
    {
        return Country::find($id);
    }
    public function update($request, $id)
    {
        return Country::find($id)->update($request);
    }
    public function delete($id): int
    {
        return Country::destroy($id);
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
            dd();
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }


    public function getCode($id)
    {
        return Country::find($id)->phonecode;
    }
}
