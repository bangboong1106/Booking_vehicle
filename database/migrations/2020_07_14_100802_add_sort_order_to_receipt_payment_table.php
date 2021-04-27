<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddSortOrderToReceiptPaymentTable extends Base
{
    protected $_table = 'm_receipt_payment';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'sort_order')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('sort_order')->nullable()->after('is_driver');
            });

            DB::unprepared('
            SET SQL_SAFE_UPDATES = 0;
            UPDATE  m_receipt_payment r
            JOIN 
            (
                SELECT  t.id, @rownum := @rownum + 1 AS rank 
                FROM m_receipt_payment t
                CROSS JOIN (select @rownum := 0) r
                WHERE type = 2
                ORDER BY is_system DESC, name
            ) as s
            ON r.id = s.id
            SET r.sort_order = s.rank;
            ');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->getTable(), 'sort_order')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
}
