<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreatePermissionCustom extends Command
{
    protected $signature = 'role:create-permission 
                {name : The name of the permission} 
                {display : The display order of the permission}
                {guard? : The name of the guard}';

    protected $description = 'Create a permission custom';

    public function handle()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate($this->argument('name'), $this->argument('display'), $this->argument('guard'));

        $this->info("Permission `{$permission->name}` created");
    }
}
