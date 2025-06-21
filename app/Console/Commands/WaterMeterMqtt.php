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
        $mqtt = null;

        try {

            $server = 'git.panchip.com';
            $port = 3306;
            $clientId = 'arrogate-dev';

            $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
            $mqtt->connect();

            $mqtt->subscribe('/watermeters/data/32', function ($topic, $message) {
                Log::channel('mqtt')->info($topic . '|' . $message);
            }, 0);

            $mqtt->subscribe('/watermeters/data/105', function ($topic, $message) {
                Log::channel('mqtt')->info($topic . '|' . $message);
            }, 0);

            $mqtt->subscribe('/watermeters/data/144', function ($topic, $message) {
                Log::channel('mqtt')->info($topic . '|' . $message);
            }, 0);

            $mqtt->loop(true);
            $mqtt->disconnect();

        } catch (\Exception $e) {
            $mqtt && $mqtt->disconnect();
            Log::error('Command failed: ' . $e->getMessage());
            return 1;
        }
    }
}
