<?php

namespace Tests\Feature\Admin;

class IndexControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->adminGet('admin');
        $response->assertStatus(200);
    }

    public function testIndexGuest()
    {
        $response = $this->get('admin');
        $response->assertRedirect('admin/login/');
    }

    public function testLoginUserForTest()
    {
        $response = $this->adminGet('admin/loginUserForTest');
        $response->assertRedirect('admin');
    }
}
