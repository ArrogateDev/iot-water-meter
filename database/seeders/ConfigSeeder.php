<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $configs[Config::WITHDRAW] = [
            'config' => [
                [
                    'key' => 'unlimited',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '不限',
                ],
                [
                    'key' => 'one_port_min',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '单口最小提现金额',
                ],
                [
                    'key' => 'one_port_max',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '单口最大提现金额',
                ],
                [
                    'key' => 'two_port_min',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '双口最小提现金额',
                ],
                [
                    'key' => 'two_port_max',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '双口最大提现金额',
                ],
                [
                    'key' => 'method',
                    'value' => 0,
                    'show' => Config::SHOW,
                    'description' => '提现方式：0-微信/1-线下结算',
                ]
            ],
        ];

        foreach ($configs as $type => $list) {
            foreach ($list as $group => $items) {
                foreach ($items as $item) {
                    if (!Config::query()->where('type', $type)->where('group', $group)->where('key', $item['key'])->exists()) {
                        $config = new Config();
                        $config->type = $type;
                        $config->group = $group;
                        $config->key = $item['key'];
                        $config->value = $item['value'];
                        $config->show = $item['show'] ?? Config::HIDDEN;
                        $config->description = $item['description'] ?? '';
                        $config->save();
                    }
                }
            }
        }
    }
}
