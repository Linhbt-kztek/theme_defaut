<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MinioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateBucketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:bucket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đây là tạo admin bằng câu lệnh';

    public function handle()
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $minio = (new MinioService)->createBucket($bucket);
        echo $minio;

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
