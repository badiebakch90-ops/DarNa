<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_auth_pages(): void
    {
        $this->get('/login')->assertOk()->assertSee('Connexion', false);
        $this->get('/register')->assertOk()->assertSee('Inscription', false);
        $this->get('/forgot-password')->assertOk()->assertSee('Mot de passe oublie', false);
    }

    public function test_user_can_register_and_return_to_the_public_site(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Member',
            'email' => 'new@darna.test',
            'phone' => '+212600000099',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new@darna.test',
            'role' => 'member',
        ]);
    }

    public function test_registration_cannot_self_assign_admin_role(): void
    {
        $this->post('/register', [
            'name' => 'Attempted Admin',
            'email' => 'attempted-admin@darna.test',
            'phone' => '+212600000098',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'attempted-admin@darna.test',
            'role' => 'member',
        ]);
    }

    public function test_admin_can_login_and_logout(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'member@darna.test',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_member_login_returns_to_home_page(): void
    {
        $user = User::factory()->create([
            'email' => 'client@darna.test',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_member_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'logout@darna.test',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_user_can_request_a_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'forgot@darna.test',
        ]);

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('status');

        $this->assertNotNull(Password::broker()->createToken($user));
    }

    public function test_password_reset_request_does_not_reveal_unknown_emails(): void
    {
        $this->post('/forgot-password', [
            'email' => 'missing@darna.test',
        ])->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();
    }

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        User::factory()->create([
            'email' => 'rate-limit@darna.test',
            'password' => 'password',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post('/login', [
                'email' => 'rate-limit@darna.test',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => 'rate-limit@darna.test',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }
}
