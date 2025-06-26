<?php

namespace App\Services;

use App\Models\WaterMeterDailyData;
use App\Models\WaterMeterMonthData;
use Carbon\Carbon;

class WaterMeterDashboard
{
    /**
     * @param int $meter_id
     * @return array
     * @throws \Exception
     */
    public static function getDashboardSingle(int $meter_id)
    {

        try {

            $result['meter_id'] = $meter_id;

            $now = Carbon::now();

            $daily_data = WaterMeterDailyData::get($meter_id, $now->copy());
            $last_daily_data = WaterMeterDailyData::get($meter_id, $now->copy()->subDay());

            $last_updated_at = Carbon::parse($daily_data->last_updated_at);

            $daily_meter_reading = $daily_data->water_meter_reading ?? 0;
            $last_daily_meter_reading = $last_daily_data->water_meter_reading ?? 0;

            $result['current_reading'] = $daily_meter_reading;
            $result['today_usage'] = bcsub($daily_meter_reading, $last_daily_meter_reading, 2);
            $result['last_updated_at'] = $last_updated_at->copy()->format('d-M-Y H:i:s');

            $month_data = WaterMeterMonthData::get($meter_id, $now->copy());
            $last_month_data = WaterMeterMonthData::get($meter_id, $now->copy()->subMonth());

            $month_meter_reading = $month_data->water_meter_reading ?? 0;
            $last_month_meter_reading = $last_month_data->water_meter_reading ?? 0;

            $result['monthly_bill'] = bcmul($month_meter_reading, 3, 2);
            $result['monthly_usage'] = $month_meter_reading;

            $result['last_month_bill'] = bcmul($last_month_meter_reading, 3, 2);
            $result['month_bill_change'] = $last_month_meter_reading > 0 ? bcdiv(bcsub($month_meter_reading, $last_month_meter_reading, 2), $last_month_meter_reading, 2) * 100 : 0;
            $result['last_month_usage'] = $last_month_meter_reading;
            $result['status'] = 1;

            $chart = [];
            $start_date = $now->copy()->subYear();

            $x_axis = $start_date->copy()->monthsUntil($now)->toArray();
            $default_x_axis = [];
            foreach ($x_axis as $value) {
                $default_x_axis[$value->copy()->format('Y-m')] = $value->copy()->format('M,y');
            }

            $results = WaterMeterMonthData::query()
                ->where('meter_id', $meter_id)
                ->whereBetween('month', [$start_date, $now])
                ->select('meter_id', 'month', 'water_meter_reading')
                ->get();

            foreach ($default_x_axis as $key => $value) {
                $chart[] = [
                    'x_axis' => $value,
                    'y_axis' => floor($results->where('month', $key)->first()->water_meter_reading ?? 0),
                ];
            }

            $result['chart'] = $chart;

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
