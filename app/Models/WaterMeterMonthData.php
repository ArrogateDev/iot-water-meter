<?php

namespace App\Models;

use Carbon\Carbon;

class WaterMeterMonthData extends Base
{

    /**
     * @param $meter_id
     * @param Carbon $carbon
     * @return WaterMeterMonthData
     */
    public static function get($meter_id, Carbon $carbon): WaterMeterMonthData
    {
        return static::firstOrCreate(
            ['meter_id' => $meter_id, 'month' => $carbon->format('Y-m')],
            ['water_meter_reading' => 0]
        );
    }
}
