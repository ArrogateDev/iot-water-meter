<?php

namespace App\Jobs;

use App\Services\WaterMeterDashboard;
use GatewayWorker\Lib\Gateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WaterMeterDataNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $meter_id;

    /**
     * Create a new job instance.
     */
    public function __construct(int $meter_id)
    {
        $this->meter_id = $meter_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $meter_id = $this->meter_id;

            Gateway::$registerAddress = '0.0.0.0:1230';

            if (Gateway::getAllClientIdCount() > 0) {
                $result = WaterMeterDashboard::getDashboardSingle($meter_id);
                $message['type'] = 'water:meter:data';
                $message['content'] = $result;
                $req_data = json_encode($message);
                Gateway::sendToAll($req_data);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
