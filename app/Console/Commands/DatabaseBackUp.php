<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    //     $filename = "backup-" . Carbon::now()->format('Y-m-d-h-i') . ".sql";

    //    $command = "mysqldump --user=" . config('database.connections.mysql.username') ." --password=" . config('database.connections.mysql.password') . " --host=" . config('database.connections.mysql.host') . " " . config('database.connections.mysql.database') . " > " . storage_path() . "/app/backup/" . $filename;

    //     $returnVar = NULL;
    //     $output  = NULL;

    //     exec($command, $output, $returnVar);

    //     \Storage::disk('google')->put($filename, file_get_contents(storage_path() . "/app/backup/" . $filename));


    //     if(file_exists(storage_path() . "/app/backup/" . $filename)):
    //         unlink(storage_path() . "/app/backup/" . $filename);
    //     endif;
    }
}
