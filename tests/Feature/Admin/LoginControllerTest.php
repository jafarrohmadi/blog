<?php

namespace Tests\Feature\Admin;

class LoginControllerTest extends TestCase
{
    public function testIndex()
    {
        $this->get('admin/login/')
            ->assertStatus(200);
    }

    public function testIndexLogin()
    {
        $this->adminGet('admin/login/index')
            ->assertRedirect('admin');
    }

    public function testLogout()
    {
        $this->loginByUserId(1, 'admin');
        $this->get('admin/logout')
            ->assertStatus(302);
        $this->assertGuest('admin');
    }
}
