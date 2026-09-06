<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\ThanaUpazilaDataTable;
use App\Http\Requests\Admin\ThanaUpazilaRequest;
use App\Repositories\DistrictRepository;
use App\Repositories\ThanaUpazilaRepository;
use Exception;
use Illuminate\Http\Request;

class ThanaUpazilaController extends Controller
{
    protected $district;
    protected $upazila;

    public function __construct(DistrictRepository $district, ThanaUpazilaRepository $upazila)
    {
        $this->district = $district;
        $this->upazila  = $upazila;
    }

    public function index(ThanaUpazilaDataTable $datatable)
    {
        $districts = $this->district->activeDistricts();
        return $datatable->render('admin.upazila.index', compact('districts'));
    }

    public function store(ThanaUpazilaRequest $request): \Illuminate\Http\JsonResponse
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
            $this->upazila->store($request->all());

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            $upazila = $this->upazila->find($id);

            $data = [
                'id'          => $upazila->id,
                'name'        => $upazila->name,
                'district_id' => $upazila->district_id,
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(ThanaUpazilaRequest $request, $id): \Illuminate\Http\JsonResponse
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
            $this->upazila->update($request->all(), $id);

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
            $this->upazila->delete($id);

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
            $this->upazila->statusChange($request);
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
