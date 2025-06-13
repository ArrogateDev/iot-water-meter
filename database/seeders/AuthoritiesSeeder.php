<?php

namespace Database\Seeders;

use App\Models\Base;
use App\Models\Manage\Authority;
use Illuminate\Database\Seeder;

class AuthoritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $authorities[] = [
            'name' => '工作台',
            'alias' => 'DashboardManage',
            'sort' => 0,
            'type' => Authority::MENU_TYPE,
            'pid' => 0,
            'children' => [
                [
                    'name' => '数据看板',
                    'alias' => 'Dashboard',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ]
            ]
        ];

        $authorities[] = [
            'name' => '代理商管理',
            'alias' => 'AgentManage',
            'sort' => 0,
            'type' => Authority::MENU_TYPE,
            'pid' => 0,
            'children' => [
                [
                    'name' => '代理商设置',
                    'alias' => 'AgentSetting',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '编辑代理设置',
                            'alias' => 'AgentSettingEdit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => '代理商列表',
                    'alias' => 'AgentList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '添加代理商',
                            'alias' => 'AgentAdd',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '编辑代理商',
                            'alias' => 'AgentEdit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '删除代理商',
                            'alias' => 'AgentDelete',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => '代理商申请列表',
                    'alias' => 'AgentApplyList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '审核代理商',
                            'alias' => 'AgentApplyAudit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
            ]
        ];

        $authorities[] = [
            'name' => '设备管理',
            'alias' => 'EquipmentManage',
            'sort' => 0,
            'type' => Authority::MENU_TYPE,
            'pid' => 0,
            'children' => [
                [
                    'name' => '设备列表',
                    'alias' => 'EquipmentList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ],
                [
                    'name' => '投递订单',
                    'alias' => 'EquipmentOrder',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ],
                [
                    'name' => '提现记录',
                    'alias' => 'FinanceList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ],
                [
                    'name' => '收入明细',
                    'alias' => 'EquipmentIncome',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ],
            ]
        ];

        $authorities[] = [
            'name' => '系统设置',
            'alias' => 'SystemManage',
            'sort' => 0,
            'type' => Authority::MENU_TYPE,
            'pid' => 0,
            'children' => [
                [
                    'name' => '角色管理',
                    'alias' => 'RoleList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '添加角色',
                            'alias' => 'RoleAdd',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '编辑角色',
                            'alias' => 'RoleEdit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '删除角色',
                            'alias' => 'RoleDelete',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => '管理员管理',
                    'alias' => 'AdminList',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '添加管理员',
                            'alias' => 'AdminAdd',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '编辑管理员',
                            'alias' => 'AdminEdit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '删除管理员',
                            'alias' => 'AdminDelete',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => '告警记录',
                    'alias' => 'AlarmRecord',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => [
                        [
                            'name' => '添加告警记录',
                            'alias' => 'AlarmRecordAdd',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '编辑告警记录',
                            'alias' => 'AlarmRecordEdit',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ],
                        [
                            'name' => '删除告警记录',
                            'alias' => 'AlarmRecordDelete',
                            'sort' => 0,
                            'type' => Authority::BUTTON_TYPE,
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => '操作日志',
                    'alias' => 'OperationLog',
                    'sort' => 0,
                    'type' => Authority::GPS_TYPE,
                    'children' => []
                ]
            ]
        ];

        $total = count($authorities);
        Authority::truncate();
        foreach ($authorities as $index => $level0_item) {
            if (!($level0 = Authority::query()->where('alias', $level0_item['alias'])->first())) {
                $level0 = new Authority();
                $level0->name = $level0_item['name'];
                $level0->alias = $level0_item['alias'];
                $level0->type = $level0_item['type'];
                $level0->pid = $level0_item['pid'];
                $level0->sort = ($total * 1000) - ($index * 1000);
                $level0->save();
            }
            foreach ($level0_item['children'] ?? [] as $l1 => $level1_item) {
                if (!($level1 = Authority::query()->where('alias', $level1_item['alias'])->first())) {
                    $level1 = new Authority();
                    $level1->name = $level1_item['name'];
                    $level1->alias = $level1_item['alias'];
                    $level1->sort = $level0->sort - (($l1 + 1) * 100);
                    $level1->type = $level1_item['type'];
                    $level1->pid = $level0->id;
                    $level1->save();
                }
                foreach ($level1_item['children'] ?? [] as $l2 => $level02_item) {
                    if (!($level2 = Authority::query()->where('alias', $level02_item['alias'])->first())) {
                        $level2 = new Authority();
                        $level2->name = $level02_item['name'];
                        $level2->alias = $level02_item['alias'];
                        $level2->sort = $level1->sort - (($l2 + 1) * 10);
                        $level2->type = $level02_item['type'];
                        $level2->pid = $level1->id;
                        $level2->save();
                    }
                    foreach ($level02_item['children'] ?? [] as $l3 => $level3_item) {
                        if (!Authority::query()->where('alias', $level3_item['alias'])->exists()) {
                            $level3 = new Authority();
                            $level3->name = $level3_item['name'];
                            $level3->alias = $level3_item['alias'];
                            $level3->sort = $level2->sort - $l3 + 1;
                            $level3->type = $level3_item['type'];
                            $level3->pid = $level2->id;
                            $level3->save();
                        }
                    }
                }
            }
        }
    }
}
