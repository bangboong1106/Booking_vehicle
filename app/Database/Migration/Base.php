<?php

namespace App\Database\Migration;

use Illuminate\Database\Migrations\Migration;

class Base extends Migration
{
    protected $_table;

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
}
