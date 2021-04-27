<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateIndexOrderHistoryRouteApprovalHistoryTable extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_history', function (Blueprint $table) {
            $table->index(['order_id']);
        });

        Schema::table('route_approval_history', function (Blueprint $table) {
            $table->index(['id']);
            $table->index(['route_id']);
        });
    }

    public function down()
    {
        Schema::table('order_history', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
        });
        Schema::table('route_approval_history', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['route_id']);
        });
    }
}
