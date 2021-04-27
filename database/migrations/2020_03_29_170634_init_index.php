<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class InitIndex extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_province', function (Blueprint $table) {
            $table->index(['province_id']);
        });
        Schema::table('good_types', function (Blueprint $table) {
            $table->index(['code']);
        });
        Schema::table('good_units', function (Blueprint $table) {
            $table->index(['code']);
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->index(['code']);
        });
        Schema::table('m_district', function (Blueprint $table) {
            $table->index(['district_id']);
        });
        Schema::table('m_ward', function (Blueprint $table) {
            $table->index(['ward_id']);
        });
        Schema::table('m_vehicle_group', function (Blueprint $table) {
            $table->index(['code']);
        });
    }

    public function down()
    {
        Schema::table('m_province', function (Blueprint $table) {
            $table->dropIndex(['province_id']);
        });
        Schema::table('good_types', function (Blueprint $table) {
            $table->dropIndex(['code']);
        });
        Schema::table('good_units', function (Blueprint $table) {
            $table->dropIndex(['code']);
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['code']);
        });
        Schema::table('m_district', function (Blueprint $table) {
            $table->dropIndex(['district_id']);
        });
        Schema::table('m_ward', function (Blueprint $table) {
            $table->dropIndex(['ward_id']);
        });
        Schema::table('m_vehicle_group', function (Blueprint $table) {
            $table->dropIndex(['code']);
        });
    }
}
