<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DivisionRepository;
use App\Repositories\CountryRepository;
use App\DataTables\Admin\DivisionDataTable;
use App\Http\Requests\Admin\DivisionRequest;

class DivisionController extends Controller
{
    protected $division;
    protected $country;

    public function __construct(DivisionRepository $division, CountryRepository $country)
    {
        $this->division = $division;
        $this->country  = $country;
    }

    public function index(DivisionDataTable $dataTable)
    {
        $countries = $this->country->activeCountries();
        return $dataTable->render('admin.division.index', compact('countries'));
    }

    public function store(DivisionRequest $request): \Illuminate\Http\JsonResponse
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
            $this->division->store($request->all());

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            $division = $this->division->find($id);

            $data = [
                'id'         => $division->id,
                'name'       => $division->name,
                'country_id' => $division->country_id,
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(DivisionRequest $request, $id): \Illuminate\Http\JsonResponse
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
            $this->division->update($request->all(), $id);

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
            $this->division->delete($id);

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
            $this->division->statusChange($request);
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
