<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAdminUsersVehicleTeamTable extends \App\Database\Migration\Create
{
    protected $_table = 'admin_users_vehicle_teams';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->unsignedInteger('admin_user_id');
            $table->unsignedInteger('vehicle_team_id');

            $table->primary(['admin_user_id', 'vehicle_team_id'],
                'admin_users_vehicle_teams_primary');
        });
    }
}
