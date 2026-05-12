<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $area = config('areas.0', 'COMPRAS');

        $response = $this->post('/register', [
            'primer_nombre' => 'Test',
            'primer_apellido' => 'User',
            'email' => 'test@example.com',
            'area' => $area,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'test.user',
        ]);
    }

    public function test_registration_username_preserves_enye(): void
    {
        $area = config('areas.0', 'COMPRAS');

        $this->post('/register', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Peña',
            'email' => 'ana.pena@example.com',
            'area' => $area,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ana.pena@example.com',
            'username' => 'ana.peña',
        ]);
    }

    public function test_registration_allows_duplicate_email(): void
    {
        $area = config('areas.0', 'COMPRAS');

        $this->post('/register', [
            'primer_nombre' => 'Uno',
            'primer_apellido' => 'Prueba',
            'email' => 'compartido@example.com',
            'area' => $area,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post('/logout');

        $this->post('/register', [
            'primer_nombre' => 'Dos',
            'primer_apellido' => 'Prueba',
            'email' => 'compartido@example.com',
            'area' => $area,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['email' => 'compartido@example.com', 'username' => 'uno.prueba']);
        $this->assertDatabaseHas('users', ['email' => 'compartido@example.com', 'username' => 'dos.prueba']);
    }
}
