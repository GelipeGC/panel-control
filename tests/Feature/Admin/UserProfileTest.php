<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Profession;
use Tests\TestCase;
use App\UserProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Felipe Guzman',
        'email' => 'felipe@developers.net',
        'profession_id' => null,
        'bio'   => 'Programdor vuejs y laravel',
        'twitter' => 'https://twitte.com/gelipegc',
    ];

    /** @test */
    function a_user_can_edit_its_profile()
    {
        $user = factory(User::class)->create();
        $user->profile()->save(factory(UserProfile::class)->make());

        $newProfession = factory(Profession::class)->create();

        $response = $this->get('/editar-perfil/');

        $response->assertStatus(200);

        $response = $this->put('/editar-perfil/', [
            'name' => 'Felipe',
            'email' => 'felipe@developers.net',
            'bio' => 'Programador vuejs y laravel',
            'twitter' => 'https://twitte.com/gelipegc',
            'profession_id' => $newProfession->id
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Felipe',
            'email' => 'felipe@developers.net',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador vuejs y laravel',
            'twitter' => 'https://twitte.com/gelipegc',
            'profession_id' => $newProfession->id
        ]);
    }

    /** @test */
    function the_user_cannot_change_its_role()
    {
        $user = factory(User::class)->create([
            'role' => 'user'
        ]);

        $response = $this->put('/editar-perfil/', $this->withData([
            'role' => 'admin'
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'user'
        ]);
    }
    
    /** @test */
    function the_user_cannot_change_its_password()
    {
        factory(User::class)->create([
            'password' => bcrypt('old123'),
        ]);

        $response = $this->put('/editar-perfil/', $this->withData([
            'email' => 'felipe@developers.net',
            'password' => 'new123'
        ]));

        $response->assertRedirect();

        $this->assertCredentials([
            'email' => 'felipe@developers.net',
            'password' => 'old123'
        ]);
    }
}