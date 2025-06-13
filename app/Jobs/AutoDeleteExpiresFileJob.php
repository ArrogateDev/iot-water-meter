<?php

namespace App\Jobs;

use App\Models\Manage\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AutoDeleteExpiresFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filename;

    private $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filename, $type)
    {
        $this->filename = $filename;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            $type = Str::of($this->type)->studly();
            $filename = $this->filename;
            $method = 'on' . $type;

            $this->$method($filename);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function onBanner($filename)
    {
        if (Admin::query()->whereJsonContains('avatar', $filename)->exists()) return;
        Storage::delete($filename);
    }
}
