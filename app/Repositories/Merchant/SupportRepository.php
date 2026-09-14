<?php

namespace App\Repositories\Merchant;

use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\SupportTicket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupportRepository 
{
    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(SupportTicket $model)
    {
        $this->model = $model;
    }

    /**
     * Get a specific ticket belonging to the logged-in merchant
     */
    public function get($id)
    {
        $user = Sentinel::getUser();
        $merchantId = ($user->user_type == 'merchant_staff')
            ? $user->merchant_id
            : ($user->merchant->id ?? null);

        return $this->model->with(['parcel', 'replies.user', 'replies.attachments', 'attachments'])
            ->where('id', $id)
            ->where('merchant_id', $merchantId)
            ->first();
    }

    /**
     * Store a new support ticket created by merchant
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $user = Sentinel::getUser();
            $merchantId = ($user->user_type == 'merchant_staff')
                ? $user->merchant_id
                : ($user->merchant->id ?? null);

            // Find parcel if tracking number provided
            $parcelId = null;
            if ($request->filled('tracking_number')) {
                $parcel = Parcel::where('parcel_no', trim($request->tracking_number))
                    ->when($merchantId, function ($q) use ($merchantId) {
                        $q->where('merchant_id', $merchantId);
                    })->first();

                if ($parcel) {
                    $parcelId = $parcel->id;
                }
            }

            // Create Ticket
            $ticket = new $this->model();
            $ticket->ticket_id       = SupportTicket::generateTicketId();
            $ticket->user_id         = $user->id;
            $ticket->user_type       = $user->user_type;
            $ticket->merchant_id     = $merchantId;
            $ticket->parcel_id       = $parcelId;
            $ticket->tracking_number = $request->tracking_number ? trim($request->tracking_number) : null;
            $ticket->ticket_type     = $request->ticket_type ?? 'parcel_issue';
            $ticket->priority        = $request->priority ?? 'medium';
            $ticket->status          = 'new';
            $ticket->subject         = $request->subject;
            $ticket->description     = $request->description;
            $ticket->save();

            // Save attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('uploads/support_tickets', $fileName, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_path' => 'storage/' . $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SupportRepository store error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing ticket created by merchant
     */
    public function update($request, $id = null)
    {
        DB::beginTransaction();
        try {
            $user = Sentinel::getUser();
            $merchantId = ($user->user_type == 'merchant_staff')
                ? $user->merchant_id
                : ($user->merchant->id ?? null);

            $ticketId = $id ?? $request->id;
            $ticket = $this->model->where('id', $ticketId)
                ->where('merchant_id', $merchantId)
                ->first();

            if (!$ticket) {
                return false;
            }

            // If ticket is already closed, merchant cannot edit
            if ($ticket->status == 'closed') {
                return false;
            }

            if ($request->filled('subject')) {
                $ticket->subject = $request->subject;
            }
            if ($request->filled('description')) {
                $ticket->description = $request->description;
            }
            if ($request->filled('priority')) {
                $ticket->priority = $request->priority;
            }
            if ($request->filled('ticket_type')) {
                $ticket->ticket_type = $request->ticket_type;
            }
            if ($request->filled('tracking_number')) {
                $ticket->tracking_number = trim($request->tracking_number);
                $parcel = Parcel::where('parcel_no', trim($request->tracking_number))
                    ->when($merchantId, function ($q) use ($merchantId) {
                        $q->where('merchant_id', $merchantId);
                    })->first();
                if ($parcel) {
                    $ticket->parcel_id = $parcel->id;
                }
            }

            $ticket->save();

            // Save new attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('uploads/support_tickets', $fileName, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_path' => 'storage/' . $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SupportRepository update error: ' . $e->getMessage());
            return false;
        }
    }
}
