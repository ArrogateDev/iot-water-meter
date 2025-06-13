<?php

namespace App\Events\Manage;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class AdminLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array
     */
    public $params;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, $alias, $type = 1)
    {
        $admin = $request->user('admin');
        $this->params = [
            'content' => json_encode($request->all()),
            'method' => $request->method(),
            'url' => $request->url(),
            'admin_id' => $admin->id,
            'username' => $admin->name,
            'useragent' => $request->userAgent(),
            'ip' => $request->ip(),
            'alias' => $alias,
            'type' => $type
        ];
    }
}
