<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Facades\CustomStorage;
use League\Flysystem\MountManager;

class S3Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:sync {from} {to} {disk}';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'S3 Sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local Amazon S3';

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
        $files = CustomStorage::disk($this->argument('from'))->allFiles($this->argument('disk'));
        foreach ($files as $file) {
            try {
                $mountManager->copy($this->argument('from') . '://' . $file, $this->argument('to') . '://' . $file);
            } catch (\Exception $exception) {
                logError($exception->getMessage());
            }
        }
    }
}