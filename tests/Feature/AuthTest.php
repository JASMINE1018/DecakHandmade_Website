<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'SecretPassword123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_login(): void
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'inactive@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => 'inactive@example.test',
            'password' => 'SecretPassword123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_deactivated_admin_session_is_terminated(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'deactivated@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $admin->update(['is_active' => false]);

        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }
}
