<?php

namespace App\Jobs;

use App\Models\WaterMeterDailyData;
use App\Models\WaterMeterData;
use App\Models\WaterMeterMonthData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WaterMeterDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $params;

    /**
     * Create a new job instance.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $params = $this->params;

        $meter_id = $params['meter_id'] ?? null;
        $date = $params['date'] ?? null;
        $time = $params['time'] ?? null;
        $water_meter_reading = $params['meter_reading'] ?? null;

        if ($meter_id === null || $date === null || $time === null || $water_meter_reading === null) return;

        try {

            $now = Carbon::parse($date . ' ' . $time);

            $daily_data = WaterMeterDailyData::get($meter_id, $now->copy());
            $month_data = WaterMeterMonthData::get($meter_id, $now->copy());

            if (!(($daily_data_lock = Cache::lock("water_meter_daily_data_lock:$daily_data->id", 60))->get())) {
                throw new \Exception('另一个任务正在处理中');
            }

            if (!(($month_data_lock = Cache::lock("water_meter_month_data_lock:$month_data->id", 60))->get())) {
                throw new \Exception('另一个任务正在处理中');
            }

            DB::beginTransaction();

            $log = new WaterMeterData();
            $log->meter_id = $meter_id;
            $log->date = $date;
            $log->time = $time;
            $log->water_meter_reading = $water_meter_reading;
            if ($log->save() === false) {
                throw new \Exception('log:failed');
            }

            if (empty($daily_data->last_updated_at) || $now->copy()->gt(Carbon::parse($daily_data->last_updated_at))) {
                $daily_data->water_meter_reading = $water_meter_reading;
                $daily_data->last_updated_at = $now;
                if ($daily_data->save() === false) {
                    throw new \Exception('daily_data:failed');
                }
            }

            if (empty($month_data->last_updated_at) || $now->copy()->gt(Carbon::parse($month_data->last_updated_at))) {
                $month_data->water_meter_reading = $water_meter_reading;
                $month_data->last_updated_at = $now;
                if ($month_data->save() === false) {
                    throw new \Exception('month_data:failed');
                }
            }

            WaterMeterDataNotificationJob::dispatch($meter_id)->afterCommit();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            if (!empty($daily_data_lock)) $daily_data_lock->release();
            if (!empty($month_data_lock)) $month_data_lock->release();
        }
    }
}
