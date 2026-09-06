<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BagRepository;
use App\DataTables\Admin\BagDataTable;
use App\Http\Requests\Admin\BagRequest;
use Brian2694\Toastr\Facades\Toastr;

class BagController extends Controller
{
    protected $bag;

    public function __construct(BagRepository $bag)
    {
        $this->bag = $bag;
    }

    public function index(BagDataTable $dataTable)
    {
        $branches = \App\Models\Branch::where('status', 'active')->get();
        return $dataTable->render('admin.bag.index', compact('branches'));
    }

    public function store(BagRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->bag->store($request->all()));
    }

    public function show($id)
    {
        $bag = $this->bag->find($id);
        if (!$bag) {
            Toastr::error(__('bag_not_found'));
            return redirect()->route('bag.index');
        }
        // Collect all parcel IDs in this bag for movement history
        $parcelIds = $bag->bagParcels->pluck('percel_id')->filter()->toArray();
        $movements = \App\Models\PercelMovement::with(['fromBranch', 'toBranch', 'sender', 'receiver'])
            ->whereIn('parcel_id', $parcelIds)
            ->where('manifest_no', $bag->bag_no)
            ->latest()
            ->get();
        return view('admin.bag.show', compact('bag', 'movements'));
    }

    public function edit($id)
    {
        $bag = $this->bag->find($id);
        if (!$bag) {
            return response()->json(['success' => false, 'message' => __('bag_not_found')]);
        }
        return view('admin.bag.edit', compact('bag'));
    }

    public function update(BagRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->bag->update($request->all(), $id));
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->bag->destroy($id));
    }

    public function addParcel(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'bag_id' => 'required|exists:bags,id',
            'barcode' => 'required|string'
        ]);

        return response()->json($this->bag->addParcel($request->bag_id, $request->barcode));
    }

    public function removeParcel($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'parcel_id' => 'required|exists:parcels,id' // Using correct table name 
        ]);
        
        return response()->json($this->bag->removeParcel($id, $request->parcel_id));
    }

    public function closeBag($id)
    {
        $result = $this->bag->closeBag($id);
        if (!empty($result['success'])) {
            Toastr::success($result['success']);
        } else {
            Toastr::error($result['error'] ?? __('something_went_wrong'));
        }
        return redirect()->route('bag.show', $id);
    }

    public function dispatchBag($id)
    {
        $result = $this->bag->dispatchBag($id);
        if (!empty($result['success'])) {
            Toastr::success($result['success']);
        } else {
            Toastr::error($result['error'] ?? __('something_went_wrong'));
        }
        return redirect()->route('bag.show', $id);
    }

    public function receiveBag($id)
    {
        $result = $this->bag->receiveBag($id);
        if (!empty($result['success'])) {
            Toastr::success($result['success']);
        } else {
            Toastr::error($result['error'] ?? __('something_went_wrong'));
        }
        return redirect()->route('bag.show', $id);
    }

    public function printManifest($id)
    {
        $bag = $this->bag->find($id);
        if (!$bag) {
            Toastr::error(__('bag_not_found'));
            return redirect()->route('bag.index');
        }
        return view('admin.bag.manifest_print', compact('bag'));
    }

    public function barcode($id)
    {
        $bag = $this->bag->find($id);
        if (!$bag) {
            Toastr::error(__('bag_not_found'));
            return redirect()->route('bag.index');
        }
        return view('admin.bag.barcode', compact('bag'));
    }
}
