<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WaterMeterMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:water-meter-mqtt';

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
        Log::info("water meter mqtt start");
        Log::info("water meter mqtt:" . time());

        try {

            while (true) {
                Log::info("water meter mqtt:" . time());
                if (random_int(1, 1000) > 990) {
                    throw new \Exception("water meter mqtt error", 5000);
                }
            }
        } catch (\Exception $e) {
            Log::error('Command failed: ' . $e->getMessage());
            exit($e->getCode());
        }
    }
}
