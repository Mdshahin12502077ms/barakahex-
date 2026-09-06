<?php

namespace App\Console\Commands;

use App\Models\Parcel;
use App\Traits\ShortenLinkTrait;
use Illuminate\Console\Command;

class ShortUrlCommand extends Command
{
    use ShortenLinkTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:short-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Short URL generated successfully.';

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
        $parcels = Parcel::where('short_url', '')->limit(100)->get();

        // foreach ($parcels as $parcel) {
        //     $parcel->short_url = $this->get_link($parcel->parcel_no);
        //     $parcel->save();
        // }
    }
}
