<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Interfaces\WithdrawInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Merchant;

class DailyPaymentRequest extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:payment-request';

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
            $merchants = Merchant::where('withdraw', 'daily')->whereNotNull('default_account_id')->get();
            $this->withdraws->paymentRequest($merchants);
            DB::commit();
            $this->info('daily_payment_requests_processed_successfully');
        } catch (\Exception $e) {
            \Log::error($e);

            DB::rollback();
            $this->error('an_error_occurred_while_processing_daily_payment_requests');
        }
    }
}
