<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\DeliveryZoneDataTable;
use App\Http\Requests\Admin\DeliveryZoneRequest;
use App\Repositories\ThanaUpazilaRepository;
use App\Repositories\DeliveryZoneRepository;
use Exception;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    protected $thana;
    protected $deliveryZone;

    public function __construct(ThanaUpazilaRepository $thana, DeliveryZoneRepository $deliveryZone)
    {
        $this->thana        = $thana;
        $this->deliveryZone = $deliveryZone;
    }

    public function index(DeliveryZoneDataTable $dataTable)
    {
        $thanas = $this->thana->activeUpazilas();
        return $dataTable->render('admin.delivery_zone.index', compact('thanas'));
    }

    public function store(DeliveryZoneRequest $request): \Illuminate\Http\JsonResponse
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
            $this->deliveryZone->store($request->all());

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            $deliveryZone = $this->deliveryZone->find($id);

            $data = [
                'id'       => $deliveryZone->id,
                'name'     => $deliveryZone->name,
                'thana_id' => $deliveryZone->thana_id,
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(DeliveryZoneRequest $request, $id): \Illuminate\Http\JsonResponse
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
            $this->deliveryZone->update($request->all(), $id);

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
            $this->deliveryZone->delete($id);

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
            $this->deliveryZone->statusChange($request);
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
