<?php

namespace App\Console\Commands;

use Workerman\Worker;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;

class Workerman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:workerman {action : action} {--start=all : start} {--d : daemon mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        global $argv;
        $action = $this->argument('action');

        //针对 Windows 一次执行，无法注册多个协议的特殊处理
        if ($action === 'single') {
            $start = $this->option('start');
            if ($start === 'register') {
                $this->startRegister();
            } elseif ($start === 'gateway') {
                $this->startGateWay();
            } elseif ($start === 'worker') {
                $this->startBusinessWorker();
            }
            Worker::runAll();

            return;
        }

        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        $worker = new BusinessWorker();
        $worker->name = 'BusinessWorker';
        $worker->count = 2;
        $worker->registerAddress = '127.0.0.1:1230';
        $worker->eventHandler = \App\Events\WorkermanEvent::class;
    }

    private function startGateWay()
    {
        $gateway = new Gateway("websocket://0.0.0.0:2350");
        $gateway->name = 'Gateway';
        $gateway->count = 2;
        $gateway->lanIp = '127.0.0.1';
        $gateway->startPort = 2300;
        $gateway->pingInterval = 30;
        $gateway->pingNotResponseLimit = 1;
        $gateway->pingData = '{"type":"heartbeat"}';
        $gateway->registerAddress = '127.0.0.1:1230';
    }

    private function startRegister()
    {
        new Register('text://0.0.0.0:1230');
    }
}
