<?php

namespace App\Repositories;
use App\Enums\StatusEnum;
use App\Models\Account\CompanyAccount;
use App\Models\Account\DeliveryManAccount;
use App\Models\DeliveryMan;
use App\Models\Image as ImageModel;
use App\Models\SupportTicket;
use App\Models\User;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\CommonHelperTrait;
use App\Traits\ImageTrait;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Image;


class AdminSupportTeamRepository 
{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(SupportTicket $model)
    {
        $this->model = $model;
    }


    public function statusChange(array $request)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->model->findOrFail($request['ticketId']);

            $allowedStatuses = ['open', 'new', 'processing', 'resolved', 'closed'];
            if (!in_array($request['status'], $allowedStatuses)) {
                return $this->formatResponse(false, 'Invalid status value.', '', null);
            }

            $now = now();
            $ticket->status = $request['status'];

            if ($request['status'] === 'resolved') {
                $ticket->resolved_at = $now;
            } elseif ($request['status'] === 'closed') {
                $ticket->closed_at = $now;
            }

            $ticket->save();

            DB::commit();
            return $this->formatResponse(true, 'Status updated successfully.', '', $ticket);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), '', null);
        }
    }
    
    public function delete($id){
        try{
              $ticket = SupportTicket::with(['merchant', 'parcel', 'assignedStaff', 'replies', 'attachments'])->findOrFail($id);
            $ticket->delete();
            return $this->formatResponse(true, 'Ticket deleted successfully.', '', null);
        }catch(\Throwable $e){
              return $this->formatResponse(false, $e->getMessage(), '', null);
        }
       
    }
}
