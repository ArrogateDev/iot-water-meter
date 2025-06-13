<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterMeterDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_meter_data', function (Blueprint $table) {
            $table->id();
            $table->string('meter_id')->comment('水表id');
            $table->date('date')->comment('日期');
            $table->time('time')->comment('时间');
            $table->decimal('water_meter_reading', 10)->default(0)->comment('水表读数');
            $table->softDeletes()->index('idx_deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_meter_data');
    }
}
