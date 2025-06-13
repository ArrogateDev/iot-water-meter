<?php

namespace App\Listeners\Manage;

use App\Models\Manage\AdminLog;
use App\Models\Manage\Authority;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminLogListener implements ShouldQueue
{
    public $queue = 'manage_queue';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        try {

            $params = $event->params;
            $alias = $params['alias'];
            $type = $params['type'];

            $title = Authority::query()->where(['alias' => $alias])->value('name');
            AdminLog::record($params, $type, $title);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
