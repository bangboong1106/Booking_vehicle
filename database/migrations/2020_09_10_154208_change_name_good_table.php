<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('goods_type')) {
            Schema::rename('good_types', 'goods_type');
        }
        if (!Schema::hasTable('goods_unit')) {
            Schema::rename('good_units', 'goods_unit');
        }
        if (!Schema::hasColumn('order_goods', 'goods_type_id')) {
            Schema::table('order_goods', function (Blueprint $table) {
                $table->renameColumn('good_types_id', 'goods_type_id');
            });
        }

        DB::unprepared("
            SET SQL_SAFE_UPDATES = 0;

            update permissions set name ='view goods_type' where name ='view good_type';
            update permissions set name ='add goods_type' where name ='add good_type';
            update permissions set name ='edit goods_type' where name ='edit good_type';
            update permissions set name ='delete goods_type' where name ='delete good_type';
            update permissions set name ='view goods_unit' where name ='view good_unit';
            update permissions set name ='add goods_unit' where name ='add good_unit';
            update permissions set name ='edit goods_unit' where name ='edit good_unit';
            update permissions set name ='delete goods_unit' where name ='delete good_unit';
            "
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('goods_type')) {
            Schema::rename('goods_type', 'good_types');
        }
        if (Schema::hasTable('goods_unit')) {
            Schema::rename('goods_unit', 'good_units');
        }
        if (Schema::hasColumn('order_goods', 'goods_type_id')) {
            Schema::table('order_goods', function (Blueprint $table) {
                $table->renameColumn('goods_type_id', 'good_types_id');
            });
        }
    }
}
