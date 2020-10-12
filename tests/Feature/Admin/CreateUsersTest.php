<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Skill;
use App\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Felipe',
        'email' => 'felipe@developers.net',
        'password' => '123456',
        'profession_id' => null,
        'bio'   => 'Programdor vuejs y laravel',
        'twitter' => 'https://twitte.com/gelipegc',
        'role' => 'user',
        'state' => 'active'
    ];
    /** @test */
    function it_loads_the_new_users_page()
    {
        $profession = Profession::factory()->create();

        $skillA = Skill::factory()->create();
        $skillB = Skill::factory()->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->profession = Profession::factory()->create();

        $skillA = Skill::factory()->create();
        $skillB = Skill::factory()->create();
        $skillC = Skill::factory()->create();

        $this->post('/usuarios/', $this->withData([
            'skills' => [$skillA->id,$skillB->id],
            'profession_id' => $this->profession->id
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Felipe',
            'email' => 'felipe@developers.net',
            'password' => '123456',
            'role'  => 'user',
            'active' => true
        ]);

        $user = User::findByEmail('felipe@developers.net');

        $this->assertDatabaseHas('user_profiles', [
            'bio'   => 'Programdor vuejs y laravel',
            'twitter' => 'https://twitte.com/gelipegc',
            'user_id' => $user->id,
            'profession_id' => $this->profession->id,

        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id
        ]);

        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillC->id
        ]);
    }

    
    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->post('/usuarios/', $this->withData([
            'twitter' => null
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Felipe',
            'email' => 'felipe@developers.net',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio'   => 'Programdor vuejs y laravel',
            'twitter' => null,
            'user_id' => User::findByEmail('felipe@developers.net')->id
        ]);
    }

    /** @test */
    function the_role_field_is_optional()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
            'role' => null
        ]))->assertRedirect('usuarios');

        $this->assertDatabaseHas('users', [
            'email' => 'felipe@developers.net',
            'role' => 'user',
        ]);
    }
    /** @test */
    function the_role_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
            'role' => 'invalid-role',
        ]))->assertSessionHasErrors('role');

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_id_field_is_optional()
    {
        $this->post('/usuarios/', $this->withData([
            'profession_id' => '',
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Felipe',
            
            'email' => 'felipe@developers.net',
            'password' => '123456',
        ]);
        
        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programdor vuejs y laravel',
            'user_id' => User::findByEmail('felipe@developers.net')->id,
            'profession_id' => null,
        ]);
    }

    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_the_validation_fails()
    {
        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [])
            ->assertRedirect('usuarios/nuevo');
            
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'name' => ''
            ]))
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'email' => ''
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'email' => 'correo-no-valido',
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        User::factory()->create([
            'email' => 'felipe@developers.net'
        ]);

        $this->post('/usuarios/', $this->withData([
                'email' => 'felipe@developers.net'
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'password' => ''
            ]))
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'profession_id' => '999'
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_skills_must_be_can_an_array()
    {
        $this->handleValidationExceptions();
        
        $this->post('/usuarios/', $this->withData([
                'skills' => 'PHP, JS'
            ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();
        
        $skillA = Skill::factory()->create();
        $skillB = Skill::factory()->create();

        $this->post('/usuarios/', $this->withData([
                'skills' => [$skillA->id, $skillB->id + 1]
            ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    
    /** @test */
    function the_state_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
            'state' => null
        ]))->assertSessionHasErrors('state');

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_state_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
            'state' => 'invalid-state',
        ]))->assertSessionHasErrors('state');

        $this->assertDatabaseEmpty('users');
    }
}
