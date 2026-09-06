<?php

namespace App\Repositories;

use App\Models\Bag;
use App\Models\BagParcel;
use App\Models\Parcel;
use App\Models\PercelMovement;
use App\Models\ParcelEvent;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BagRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Bag $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Bag::with(['fromBranch', 'toBranch', 'creator'])->orderByDesc('id')->get();
    }

    public function getActiveBags()
    {
        return Bag::whereIn('status', ['open', 'in_transit'])->orderByDesc('id')->get();
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            // Generate Bag No (e.g., BAG-00001)
            $lastBag = Bag::orderBy('id', 'desc')->first();
            $nextId = $lastBag ? $lastBag->id + 1 : 1;
            $bagNo = 'BAG-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $bag = new Bag();
            $bag->bag_no = $bagNo;
            $bag->from_branch_id = $request['from_branch_id'];
            $bag->to_branch_id = $request['to_branch_id'];
            $bag->max_capacity = $request['max_capacity'] ?? 50;
            $bag->note = $request['note'] ?? null;
            $bag->status = 'open';
            $bag->total_parcels = 0;
            $user = Sentinel::check();
            $bag->created_by = $user ? $user->id : 1; 

            $bag->save();

            DB::commit();
            return ['success' => __('bag_created_successfully')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function find($id)
    {
        return Bag::with(['fromBranch', 'toBranch', 'creator', 'receiver', 'deliveryMan', 'bagParcels.parcel'])->find($id);
    }

    public function update($request, $id)
    {
        try {
            $bag = Bag::find($id);
            if (!$bag) {
                return ['error' => __('bag_not_found')];
            }
            if ($bag->status != 'open') {
                return ['error' => __('can_only_update_open_bags')];
            }

            $bag->from_branch_id = $request['from_branch_id'];
            $bag->to_branch_id = $request['to_branch_id'];
            $bag->max_capacity = $request['max_capacity'] ?? $bag->max_capacity;
            $bag->note = $request['note'] ?? $bag->note;
            $bag->save();

            return ['success' => __('bag_updated_successfully')];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function destroy($id)
    {
        try {
            $bag = Bag::find($id);
            if (!$bag) {
                return ['error' => __('bag_not_found')];
            }
            if ($bag->status != 'open') {
                return ['error' => __('can_only_delete_open_bags')];
            }
            $bag->delete();
            return ['success' => __('bag_deleted_successfully')];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function addParcel($bagId, $barcode)
    {
        DB::beginTransaction();
        try {
            $bag = Bag::find($bagId);
            if (!$bag || $bag->status != 'open') {
                return ['success' => false, 'message' => __('bag_must_be_open')];
            }

            if ($bag->max_capacity > 0 && $bag->total_parcels >= $bag->max_capacity) {
                return ['success' => false, 'message' => __('bag_capacity_full')];
            }

            $parcel = Parcel::where('parcel_no', $barcode)->orWhere('id', $barcode)->first();
            
            if (!$parcel) {
                return ['success' => false, 'message' => __('parcel_not_found')];
            }

            // Only allow parcels that are warehouse-accepted and ready for branch transfer
            $allowedStatuses = ['received', 'transferred-received-by-branch', 'returned-to-warehouse'];

            if (!in_array($parcel->status, $allowedStatuses)) {
                return ['success' => false, 'message' => __('parcel_must_be_received_before_adding_to_bag') . ' (Current: ' . __($parcel->status) . ')'];
            }

            if (BagParcel::where('bag_id', $bag->id)->where('percel_id', $parcel->id)->exists()) {
                return ['success' => false, 'message' => __('parcel_already_in_this_bag')];
            }

            $existingBagParcel = BagParcel::where('percel_id', $parcel->id)
                ->whereHas('bag', function ($query) {
                    $query->whereIn('status', ['open', 'in_transit', 'closed']);
                })->first();

            if ($existingBagParcel) {
                return ['success' => false, 'message' => __('parcel_already_in_another_active_bag')];
            }

            $bagParcel = new BagParcel();
            $bagParcel->bag_id = $bag->id;
            $bagParcel->percel_id = $parcel->id;
            $bagParcel->save();


        
            $bag->total_parcels = BagParcel::where('bag_id', $bag->id)->count();
            $bag->save();
   
            DB::commit();

            return [
                'success' => true, 
                'message' => __('parcel_added_successfully'),
                'parcel' => $parcel
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function removeParcel($bagId, $parcelId)
    {
        DB::beginTransaction();
        try {
            $bag = Bag::find($bagId);
            if (!$bag || $bag->status != 'open') {
                return ['success' => false, 'message' => __('bag_must_be_open')];
            }

            $bagParcel = BagParcel::where('bag_id', $bagId)->where('percel_id', $parcelId)->first();
            if ($bagParcel) {
                $bagParcel->delete();
                $bag->total_parcels = BagParcel::where('bag_id', $bag->id)->count();
                $bag->save();
            }

            DB::commit();
            return ['success' => true, 'message' => __('parcel_removed_successfully')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function closeBag($id)
    {
        $bag = Bag::find($id);
        if ($bag && $bag->status == 'open') {
            $bag->status = 'closed';
            $bag->save();
            return ['success' => __('bag_closed_successfully')];
        }
        return ['error' => __('invalid_bag_status')];
    }

    public function dispatchBag($id)
    {
        DB::beginTransaction();
        try {
            $bag = Bag::with('fromBranch', 'bagParcels.parcel')->find($id);
            if ($bag && in_array($bag->status, ['open', 'closed'])) {
                $bag->status = 'in_transit';
                $bag->dispatched_at = now();
                $bag->save();

                $user = Sentinel::check();
                $userId = $user ? $user->id : null;

                foreach ($bag->bagParcels as $bagParcel) {
                    $parcel = $bagParcel->parcel;
                    if ($parcel) {
                        // Update parcel location and status
                        $parcel->location = $bag->fromBranch ? $bag->fromBranch->name : 'Dispatching Branch';
                        $parcel->status = 'transferred-to-branch';
                        $parcel->transfer_to_branch_id = $bag->to_branch_id;
                        $parcel->save();

                        // Create PercelMovement record
                        PercelMovement::create([
                            'parcel_id'       => $parcel->id,
                            'tracking_number' => $parcel->parcel_no,
                            'from_branch_id'  => $bag->from_branch_id,
                            'to_branch_id'    => $bag->to_branch_id,
                            'sent_by'         => $userId,
                            'sent_at'         => now(),
                            'status'          => 'in_transit',
                            'manifest_no'     => $bag->bag_no,
                            'note'            => 'Dispatched via bag: ' . $bag->bag_no,
                        ]);

                        // Create ParcelEvent
                        ParcelEvent::create([
                            'parcel_id'  => $parcel->id,
                            'user_id'    => $userId,
                            'branch_id'  => $bag->to_branch_id,
                            'title'      => 'parcel_transferred_to_branch_assigned_event',
                        ]);
                    }
                }

                DB::commit();
                return ['success' => __('bag_dispatched_successfully')];
            }
            DB::rollBack();
            return ['error' => __('invalid_bag_status')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function receiveBag($id)
    {
        DB::beginTransaction();
        try {
            $bag = Bag::with('toBranch', 'bagParcels.parcel')->find($id);
            if ($bag && $bag->status == 'in_transit') {
                $bag->status = 'received';
                $user = Sentinel::check();
                $bag->received_by = $user ? $user->id : null;
                $bag->received_at = now();
                $bag->save();

                $user = Sentinel::check();
                $userId = $user ? $user->id : null;
                $bag->received_by = $userId;
                $bag->received_at = now();
                $bag->save();

                foreach ($bag->bagParcels as $bagParcel) {
                    $parcel = $bagParcel->parcel;
                    if ($parcel) {
                        // Update parcel location, status, branch
                        $parcel->location = $bag->toBranch ? $bag->toBranch->name : 'Receiving Branch';
                        $parcel->status = 'transferred-received-by-branch';
                        $parcel->branch_id = $bag->to_branch_id;
                        $parcel->transfer_to_branch_id = null;
                        $parcel->save();

                        // Close the active PercelMovement
                        $activeMovement = PercelMovement::where('parcel_id', $parcel->id)
                            ->where('status', 'in_transit')
                            ->where('manifest_no', $bag->bag_no)
                            ->latest()
                            ->first();
                        if ($activeMovement) {
                            $activeMovement->update([
                                'received_by' => $userId,
                                'received_at' => now(),
                                'status'      => 'received',
                            ]);
                        }

                        // Create ParcelEvent
                        ParcelEvent::create([
                            'parcel_id'  => $parcel->id,
                            'user_id'    => $userId,
                            'branch_id'  => $bag->to_branch_id,
                            'title'      => 'parcel_transferred_to_branch_event',
                        ]);
                    }
                }

                DB::commit();
                return ['success' => __('bag_received_successfully')];
            }
            DB::rollBack();
            return ['error' => __('invalid_bag_status')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}
