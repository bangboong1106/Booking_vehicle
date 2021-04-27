<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\VehicleConfigFile;
use Illuminate\Support\Facades\DB;

class UpdateValueDefaultColumn extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->whereNull('is_collected_documents')->update(['is_collected_documents' => '0']);
        DB::table('orders')->whereNull('status_collected_documents')->update(['status_collected_documents' => config('constant.CHUA_THU_DU')]);
    }
}
