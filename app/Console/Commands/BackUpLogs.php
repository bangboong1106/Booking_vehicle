<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Facades\CustomStorage;
use League\Flysystem\MountManager;

class BackUpLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:backup';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'BackUpLogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up logs to Amazon S3';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mountManager = new MountManager([
            's3' => CustomStorage::disk('s3')->getDriver(),
            'local' => CustomStorage::disk('local')->getDriver(),
        ]);
        $files = CustomStorage::disk('local')->allFiles('logs');
        foreach ($files as $file) {
            $mountManager->copy('local://' . $file, 's3://' . $file . '.' . time());
        }
    }
}