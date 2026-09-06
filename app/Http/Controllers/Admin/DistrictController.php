<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\DistrictDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DistrictRequest;
use App\Repositories\DivisionRepository;
use App\Repositories\DistrictRepository;
use Exception;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
     protected $division;
     protected $district;
     public function __construct(DivisionRepository $division,DistrictRepository $district){
        $this->division = $division;
        $this->district = $district;
     }
    public function index(DistrictDataTable $dataTable)
    {

       $divisions = $this->division->activeDivisions();
       return $dataTable->render('admin.district.index',compact('divisions'));
    }

    public function store(DistrictRequest$request): \Illuminate\Http\JsonResponse
    {
       if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->district->store($request->all());

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            $district = $this->district->find($id);

            $data = [
                'id'          => $district->id,
                'name'        => $district->name,
                'division_id' => $district->division_id,
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(DistrictRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->district->update($request->all(), $id);

            return response()->json(['success' => __('update_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->district->delete($id);

            return response()->json(['success' => __('delete_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function statusChange(Request $request): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status'  => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title'   => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->district->statusChange($request);
            $data = [
                'status'  => 200,
                'message' => __('update_successful'),
                'title'   => 'success',
            ];

            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'status'  => 400,
                'message' => $e->getMessage(),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }
}
