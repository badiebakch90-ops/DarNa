<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create([
            'name' => 'Member Client',
            'email' => 'member-client@darna.test',
        ]);

        $this->actingAs($admin)
            ->get('/backoffice/users')
            ->assertOk()
            ->assertSee('Gestion des comptes', false)
            ->assertSee($member->email, false);
    }

    public function test_member_cannot_access_user_management_page(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get('/backoffice/users')
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Acces reserve aux administrateurs.');
    }

    public function test_member_cannot_access_user_management_actions(): void
    {
        $member = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($member)
            ->get("/backoffice/users/{$otherUser->id}/edit")
            ->assertRedirect('/');

        $this->actingAs($member)
            ->put("/backoffice/users/{$otherUser->id}", [
                'name' => 'Blocked Update',
                'email' => 'blocked-update@darna.test',
                'phone' => '+212600000124',
                'role' => User::ROLE_ADMIN,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect('/');

        $this->actingAs($member)
            ->delete("/backoffice/users/{$otherUser->id}")
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
        ]);
        $this->assertSame(User::ROLE_MEMBER, $otherUser->fresh()->role);
    }

    public function test_guest_is_redirected_to_login_for_user_management_routes(): void
    {
        $user = User::factory()->create();

        $this->get('/backoffice/users')->assertRedirect('/login');
        $this->get("/backoffice/users/{$user->id}/edit")->assertRedirect('/login');
    }

    public function test_admin_can_update_another_user_account(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create([
            'email' => 'guest@darna.test',
        ]);

        $this->actingAs($admin)
            ->put("/backoffice/users/{$member->id}", [
                'name' => 'Projet Manager',
                'email' => 'manager@darna.test',
                'phone' => '+212600000123',
                'role' => User::ROLE_ADMIN,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertRedirect("/backoffice/users/{$member->id}/edit");

        $member->refresh();

        $this->assertSame('Projet Manager', $member->name);
        $this->assertSame('manager@darna.test', $member->email);
        $this->assertSame('+212600000123', $member->phone);
        $this->assertSame(User::ROLE_ADMIN, $member->role);
        $this->assertTrue(Hash::check('NewPassword123!', $member->password));
    }

    public function test_admin_cannot_remove_own_admin_role(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'self-admin@darna.test',
        ]);

        User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from("/backoffice/users/{$admin->id}/edit")
            ->put("/backoffice/users/{$admin->id}", [
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'role' => User::ROLE_MEMBER,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect("/backoffice/users/{$admin->id}/edit")
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    public function test_admin_can_delete_another_admin_account(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create([
            'email' => 'second-admin@darna.test',
        ]);

        $this->actingAs($admin)
            ->delete("/backoffice/users/{$otherAdmin->id}")
            ->assertRedirect('/backoffice/users');

        $this->assertDatabaseMissing('users', [
            'id' => $otherAdmin->id,
        ]);
    }

    public function test_admin_cannot_delete_the_connected_account(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete("/backoffice/users/{$admin->id}")
            ->assertRedirect("/backoffice/users/{$admin->id}/edit")
            ->assertSessionHas('danger');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
