<?php

namespace App\Http\Controllers\DeliveryMan;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Repositories\DeliveryMan\MyAccountRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $accountRepo;

    public function __construct(MyAccountRepository $accountRepo)
    {
        $this->accountRepo = $accountRepo;
    }

    public function index(Request $request)
    {
        $summary = $this->accountRepo->summary();
        $statements = $this->accountRepo->statements($request);

        return view('deliveryman.accounts.index', compact('summary', 'statements'));
    }
}
