<?php

namespace App\Console\Commands;

use App\Traits\PaperFlyParcel;
use Illuminate\Console\Command;

class PaperflyCheckStatus extends Command
{
    use PaperFlyParcel;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paperfly:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update Paperfly parcels status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $this->updatePaperflyParcel();
    }
}
