<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class SetPermisionServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:permisionServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cấp quyền truy cập cho file';

    public function handle()
    {
        $output = null;
        $resultCode = null;

        exec('chmod -R 777 /var/www/html/storage', $output, $resultCode);

        if ($resultCode === 0) {
            $this->info(PHP_EOL . 'Permissions set successfully for storage folder.');
        } else {
            $this->error(PHP_EOL . 'Failed to set permissions for storage folder.');
        }
        return $this::SUCCESS;
    }
}
