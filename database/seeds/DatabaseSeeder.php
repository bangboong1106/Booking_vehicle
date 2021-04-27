<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $this->call([
            AdminUserInfoTableSeeder::class,
//            MProvinceTableSeeder::class,
//            MDistrictTableSeeder::class,
//            MWardTableSeeder::class,
            DriverConfigFileTableSeeder::class,
            VehicleConfigFileTableSeeder::class,
            VehicleConfigSpecificationTableSeeder::class,
            SystemCodeConfigTableSeeder::class,
            SystemCurrencyTableSeeder::class,
//            RoleAndPermissionSeeder::class,
//            SubRoleAndPermissionSeeder::class,
            DomainConfigSeeder::class,
            TPApiConfigTableSeeder::class,
//            ReceiptPaymentTableSeeder::class, Cơ chế tree => Duplicate dữ liệu khi seed
            ReportProcedureSeeder::class,
            SystemConfigSeeder::class,
            GPSCompanySeeder::class,
            UpdateValueDefaultColumn::class,
            TemplatesLayoutsTableSeeder::class,
            ReportVehicleJourneyProcedureSeeder::class,
            ExcelColumnConfigTableSeeder::class,
            PermissionSeeder::class,
            MigrateOldPermissionSeeder::class,
            CustomerPermissionSeeder::class,
            CustomerRoleSeeder::class,
        ]);
    }
}


