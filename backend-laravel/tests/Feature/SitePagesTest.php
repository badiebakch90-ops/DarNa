<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_successfully(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Sejourner au Maroc', false)
            ->assertSee('Creer ton compte ou te connecter', false);
    }

    public function test_property_page_renders_successfully(): void
    {
        $this->get('/stays/riad-al-baraka')
            ->assertOk()
            ->assertSee('Fiche logement', false);
    }

    public function test_reservation_page_renders_successfully(): void
    {
        $this->get('/reservation/appartement-vue-mer')
            ->assertOk()
            ->assertSee('Reservation', false)
            ->assertSee('Tu n as pas encore de compte ?', false);
    }

    public function test_backoffice_reservations_page_renders_successfully(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/backoffice/reservations')
            ->assertRedirect('/dashboard');
    }

    public function test_non_admin_user_cannot_access_the_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/');
    }
}
