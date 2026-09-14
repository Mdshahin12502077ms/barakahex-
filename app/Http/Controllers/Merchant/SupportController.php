<?php

namespace App\Http\Controllers\Merchant;
use App\DataTables\SupportDataTable;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;



use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Repositories\Merchant\SupportRepository;

class SupportController extends Controller
{

    protected $supportRepo;


    public function __construct(SupportRepository $supportRepo){
        $this->supportRepo=$supportRepo;
    }



    public function index(SupportDataTable $dataTable)
    {
        $user = Sentinel::getUser();
        $merchantId = ($user->user_type == 'merchant_staff')
            ? $user->merchant_id
            : ($user->merchant->id ?? $user->id);

        $total_ticket = SupportTicket::where('merchant_id', $merchantId)->count();
        $processing_ticket = SupportTicket::where('merchant_id', $merchantId)->where('status', 'processing')->count();
        $resolved_ticket = SupportTicket::where('merchant_id', $merchantId)->where('status', 'resolved')->count();
        $closed_ticket = SupportTicket::where('merchant_id', $merchantId)->where('status', 'closed')->count();

        return $dataTable->render('merchant.support.index', compact('total_ticket', 'processing_ticket', 'resolved_ticket', 'closed_ticket'));
    }

    public function create(){
        return view('merchant.support.create');
    }

    public function store(Request $request)
    {
        try {
            if ($this->supportRepo->store($request)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => __('Ticket created successfully'),
                        'route'   => route('merchant.support-tickets.index'),
                    ]);
                }
                \Brian2694\Toastr\Facades\Toastr::success(__('Ticket created successfully'));
                return redirect()->route('merchant.support-tickets.index');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'error'  => __('Something went wrong, please try again')
                    ]);
                }
                \Brian2694\Toastr\Facades\Toastr::error(__('Something went wrong, please try again'));
                return back()->withInput();
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'error'  => $e->getMessage()
                ]);
            }
            \Brian2694\Toastr\Facades\Toastr::error($e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $ticket = $this->supportRepo->get($id);
        if (!$ticket) {
            \Brian2694\Toastr\Facades\Toastr::error(__('Ticket not found'));
            return redirect()->route('merchant.support-tickets.index');
        }
        return view('merchant.support.show', compact('ticket'));
    }

}


