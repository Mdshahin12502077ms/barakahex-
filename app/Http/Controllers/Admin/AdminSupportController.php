<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AdminSupportDataTables;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Repositories\AdminSupportTeamRepository;
use App\Traits\RepoResponseTrait;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
class AdminSupportController extends Controller
{
    use RepoResponseTrait;

    protected $supportRepo;

    public function __construct(AdminSupportTeamRepository $supportRepo)
    {
        $this->supportRepo = $supportRepo;
    }

    public function index(AdminSupportDataTables $dataTable)
    {
        $counts = SupportTicket::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return $dataTable->render('admin.supports.index', compact('counts'));
    }

    public function StatusUpdate(Request $request)
    {
        $allowedStatuses = ['open', 'new', 'processing', 'resolved', 'closed'];

        $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);
          
  

        $result = $this->supportRepo->statusChange($request->all());

        if ($result->status) {
            return response()->json([
                'status'  => 200,
                'message' => $result->msg,
            ]);
        }

        return response()->json([
            'status'  => 422,
            'message' => $result->msg,
        ], 422);
    }

    public function show($id)
    {
        $ticket = \App\Models\SupportTicket::with(['merchant', 'parcel', 'assignedStaff', 'replies', 'attachments'])->findOrFail($id);
        return view('admin.supports.show', compact('ticket'));
    }

    public function Delete($id)
    {
        $result = $this->supportRepo->delete($id);
        if ($result->status) {
            Toastr::success($result->msg);
        } else {
            Toastr::error($result->msg);
        }
        return back();
    }
}
