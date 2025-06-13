<?php

namespace App\Console\Commands;

use App\Models\WaterMeterData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DataCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $no = 'WaterMeter00';
        for ($i = 0; $i < 5; $i++) {
            $last = WaterMeterData::query()->where('meter_id', $no . $i)->orderByDesc('id')->first();
            $water_meter_reading = $last->water_meter_reading ?? 0;
            $log = new WaterMeterData();
            $log->meter_id = $no . $i;
            $log->date = $now->copy()->toDateString();
            $log->time = $now->copy()->format('H:i:s');
            $log->water_meter_reading = $water_meter_reading + rand(0, 10) / 10;
            $log->save();
        }
    }
}
