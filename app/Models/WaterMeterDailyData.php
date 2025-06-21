<?php

namespace App\Models;

use Carbon\Carbon;

class WaterMeterDailyData extends Base
{

    /**
     * @param $meter_id
     * @param Carbon $carbon
     * @return WaterMeterDailyData
     */
    public static function get($meter_id, Carbon $carbon): WaterMeterDailyData
    {
        return static::firstOrCreate(
            ['meter_id' => $meter_id, 'date' => $carbon->toDateString()],
            ['water_meter_reading' => 0]
        );
    }
}
