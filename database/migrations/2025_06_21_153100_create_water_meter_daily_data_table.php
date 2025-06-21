<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('water_meter_daily_data', function (Blueprint $table) {
            $table->id();
            $table->string('meter_id')->comment('水表id');
            $table->date('date')->comment('日期');
            $table->decimal('water_meter_reading', 10)->default(0)->comment('水表读数');
            $table->timestamp('last_updated_at')->nullable()->comment('最后更新时间');
            $table->softDeletes()->index('idx_deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_meter_daily_data');
    }
};
