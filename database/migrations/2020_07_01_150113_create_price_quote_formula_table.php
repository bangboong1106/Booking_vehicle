<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePriceQuoteFormulaTable extends \App\Database\Migration\Create
{
    protected $_table = 'price_quote_formula';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('price_quote_id');
            $table->integer('location_group_destination_id')->nullable();
            $table->integer('location_group_arrival_id')->nullable();
            $table->integer('distance')->nullable();
            $table->integer('vehicle_group_id')->nullable();
            $table->decimal('weight_from', 18, 4)->nullable();
            $table->decimal('weight_to', 18, 4)->nullable();
            $table->decimal('volume_from', 18, 4)->nullable();
            $table->decimal('volume_to', 18, 4)->nullable();
            $table->string('operator')->nullable();
            $table->decimal('price', 18, 4)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_quote_formula');
    }
}
