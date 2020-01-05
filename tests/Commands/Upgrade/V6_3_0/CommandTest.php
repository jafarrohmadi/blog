<?php

namespace Tests\Commands\Upgrade\V6_3_0;

use Artisan;

class CommandTest extends \Tests\Commands\Upgrade\TestCase
{
    public function testCommand()
    {
        Artisan::call('upgrade:v6.3.0');

        $this->assertDatabaseHasColumns('comments', ['is_audited']);

        $configs = [
            [
                'id'         => 173,
                'name'       => 'bjyblog.comment_audit',
                'value'      => 'false',
                'created_at' => '2019-10-21 22:45:00',
                'updated_at' => '2019-10-21 22:45:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 174,
                'name'       => 'services.baidu.appid',
                'value'      => '',
                'created_at' => '2019-10-21 22:45:00',
                'updated_at' => '2019-10-21 22:45:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 175,
                'name'       => 'services.baidu.appkey',
                'value'      => '',
                'created_at' => '2019-10-21 22:45:00',
                'updated_at' => '2019-10-21 22:45:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 176,
                'name'       => 'services.baidu.secret',
                'value'      => '',
                'created_at' => '2019-10-21 22:45:00',
                'updated_at' => '2019-10-21 22:45:00',
                'deleted_at' => null,
            ],
        ];

        foreach ($configs as $config) {
            $this->assertDatabaseHas('configs', $config);
        }
    }
}
