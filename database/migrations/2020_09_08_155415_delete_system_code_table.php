<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;


class DeleteSystemCodeTable extends Base
{
    protected $_table = 'system_code';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->getTable());
    }
}
