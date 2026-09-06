<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\AreaDataTable;
use App\Http\Requests\Admin\AreaRequest;
use App\Repositories\AreaRepository;
use App\Repositories\DeliveryZoneRepository;
use App\Repositories\ThanaUpazilaRepository;
use App\Repositories\DistrictRepository;
use App\Imports\AreaImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AreaController extends Controller
{
    protected $area;
    protected $deliveryZone;
    protected $thana;
    protected $district;

    public function __construct(
        AreaRepository $area,
        DeliveryZoneRepository $deliveryZone,
        ThanaUpazilaRepository $thana,
        DistrictRepository $district
    ) {
        $this->area         = $area;
        $this->deliveryZone = $deliveryZone;
        $this->thana        = $thana;
        $this->district     = $district;
    }

    public function index(AreaDataTable $dataTable)
    {
        $deliveryZones = $this->deliveryZone->activeDeliveryZones();
        $thanas        = $this->thana->activeUpazilas();
        $districts     = $this->district->activeDistricts();

        return $dataTable->render('admin.area.index', compact('deliveryZones', 'thanas', 'districts'));
    }

    public function store(AreaRequest $request): \Illuminate\Http\JsonResponse
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
            $this->area->store($request->all());

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            $area = $this->area->find($id);

            $data = [
                'id'               => $area->id,
                'name'             => $area->name,
                'delivery_zone_id' => $area->delivery_zone_id,
                'thana_id'         => $area->thana_id ?? ($area->deliveryZone->thana_id ?? null),
                'district_id'      => $area->district_id ?? ($area->deliveryZone->thana->district_id ?? null),
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(AreaRequest $request, $id): \Illuminate\Http\JsonResponse
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
            $this->area->update($request->all(), $id);

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
            $this->area->delete($id);

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
            $this->area->statusChange($request);
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

    public function import(Request $request)
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return $request->ajax() ? response()->json($data) : back()->with('danger', __('this_function_is_disabled_in_demo_server'));
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            Excel::import(new AreaImport, $request->file('file'));

            if ($request->ajax()) {
                return response()->json([
                    'success' => __('successfully_imported'),
                    'route'   => route('areas.index'),
                ]);
            }

            Toastr::success(__('successfully_imported'));
            return back()->with('success', __('successfully_imported'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            $errorMessage = implode('<br>', $messages);

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage,
                ]);
            }

            Toastr::error($errorMessage);
            return back()->with('danger', $errorMessage);
        } catch (\Throwable $e) {
            Log::error('Area Import Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => $e->getMessage(),
                ]);
            }

            Toastr::error($e->getMessage());
            return back()->with('danger', $e->getMessage());
        }
    }

    public function downloadSample(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="area_import_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['division', 'district', 'thana', 'delivery_zone', 'area_name']);
            fputcsv($file, ['Dhaka', 'Dhaka', 'Uttara', 'Sector Zone', 'Sector 3']);
            fputcsv($file, ['Dhaka', 'Dhaka', 'Dhanmondi', 'Dhanmondi Zone', 'Road 27']);
            fputcsv($file, ['Dhaka', 'Gazipur', 'Sadar', 'Chowrasta Zone', 'Chandana Chowrasta']);
            fputcsv($file, ['Chattogram', 'Chattogram', 'Kotwali', 'Station Zone', 'New Market']);
            fputcsv($file, ['Rajshahi', 'Bogura', 'Shibganj', 'Mokamtala Zone', 'Mokamtala Bazar']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
