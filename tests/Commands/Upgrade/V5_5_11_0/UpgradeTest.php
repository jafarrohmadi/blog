<?php

namespace Tests\Commands\Upgrade\V5_5_11_0;

class UpgradeTest extends \Tests\Commands\Upgrade\TestCase
{
    public function testUpgrade()
    {
        $this->artisan('bjyblog:update')->assertExitCode(0);
    }
}
