<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Interfaces\WithdrawInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Merchant;

class MonthlyPaymentRequest extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:payment-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $withdraws;

    public function __construct(WithdrawInterface $withdraws)
    {
        $this->withdraws = $withdraws;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $merchants = Merchant::where('withdraw', 'monthly')->whereNotNull('default_account_id')->get();
            $this->withdraws->paymentRequest($merchants);

            DB::commit();
            $this->info('monthly_payment_requests_processed_successfully');
        } catch (\Exception $e) {
            \Log::error(123);
            DB::rollback();
            $this->error('an_error_occurred_while_processing_monthly_payment_requests');
        }
    }
}
