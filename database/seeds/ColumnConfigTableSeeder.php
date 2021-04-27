<?php

use App\Model\Entities\ColumnConfig;
use Illuminate\Database\Seeder;

class ColumnConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        ColumnConfig::truncate();
    }
}
