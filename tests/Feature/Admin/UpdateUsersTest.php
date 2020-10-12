<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Skill;
use App\Profession;
use Tests\TestCase;
use App\UserProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Felipe Guzman',
        'email' => 'felipe@developers.net',
        'password' => '123456',
        'profession_id' => null,
        'bio'   => 'Programdor vuejs y laravel',
        'twitter' => 'https://twitte.com/gelipegc',
        'role' => 'user',
        'state' => 'active'
    ];
    
    /** @test */
    function it_loads_the_edit_user_page()
    {
        $user = User::factory()->create();

        $this->get("/usuarios/{$user->id}/editar") // usuarios/5/editar
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }

    /** @test */
    function it_updates_a_user()
    {
        $user = User::factory()->create();
        $oldProfession = Profession::factory()->create();
        $user->profile->update([
            'profession_id' => $oldProfession->id
        ]);

        $oldSkill1 = Skill::factory()->create();
        $oldSkill2 = Skill::factory()->create();
        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $newProfession = Profession::factory()->create();
        $newSkill1 = Skill::factory()->create();
        $newSkill2 = Skill::factory()->create();
        
        $this->put("/usuarios/{$user->id}", $this->withData([
            'role' => 'admin',
            'state' => 'inactive',
            'profession_id' => $newProfession->id,
            'skills' => [$newSkill1->id, $newSkill2->id],
            
        ]))->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Felipe Guzman',
            'email' => 'felipe@developers.net',
            'password' => '123456',
            'role' => 'admin',
            'active' => false
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'bio'   => 'Programdor vuejs y laravel',
            'twitter' => 'https://twitte.com/gelipegc',
            'profession_id' => $newProfession->id,
        ]);

        $this->assertDatabaseCount('user_skill', 2);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill1->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill2->id
        ]);
    }
    /** @test */
    function it_detaches_all_the_skills_if_none_is_checked()
    {
        $user = User::factory()->create();
       
        $oldSkill1 = Skill::factory()->create();
        $oldSkill2 = Skill::factory()->create();
        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

    
        
        $this->put("/usuarios/{$user->id}", $this->withData([]))->assertRedirect("/usuarios/{$user->id}");

        

        $this->assertDatabaseEmpty('user_skill');
    }

    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = User::factory()->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'name' => '',
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'felipe@developers.net']);
    }

    

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $user = User::factory()->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'email' => 'correo-no-valido',
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Felipe']);
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();


        User::factory()->create([
            'email' => 'existing-email@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'felipe@developers.net'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'email' => 'existing-email@example.com',
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        //
    }

    /** @test */
    function the_users_email_can_stay_the_same()
    {
        $user = User::factory()->create([
            'email' => 'felipe@developers.net'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'name' => 'Felipe Guzman',
                'email' => 'felipe@developers.net',
            ]))
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertDatabaseHas('users', [
            'name' => 'Felipe Guzman',
            'email' => 'felipe@developers.net',
        ]);
    }

    /** @test */
    function the_password_is_optional()
    {
        $oldPassword = 'CLAVE_ANTERIOR';

        $user = User::factory()->create([
            'password' => bcrypt($oldPassword)
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'password' => ''
            ]))
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertCredentials([
            'name' => 'Felipe Guzman',
            'email' => 'felipe@developers.net',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

    /** @test */
    function the_role_is_required()
    {
        $this->handleValidationExceptions();
 
        $user = User::factory()->create();
 
        $this->from("usuarios/{$user->id}/editar")
             ->put("usuarios/{$user->id}", $this->withData([
                 'role' => '',
             ]))
             ->assertRedirect("usuarios/{$user->id}/editar")
             ->assertSessionHasErrors(['role']);
 
        $this->assertDatabaseMissing('users', ['email' => 'felipe@developers.net']);
    }

    /** @test */
    function the_bio_is_required()
    {
        $this->handleValidationExceptions();
 
        $user = User::factory()->create();
 
        $this->from("usuarios/{$user->id}/editar")
             ->put("usuarios/{$user->id}", $this->withData([
                 'bio' => '',
             ]))
             ->assertRedirect("usuarios/{$user->id}/editar")
             ->assertSessionHasErrors(['bio']);
 
        $this->assertDatabaseMissing('users', ['email' => 'felipe@developers.net']);
    }

    /** @test */
    function the_state_is_required()
    {
        $this->handleValidationExceptions();
 
        $user = User::factory()->create();
 
        $this->from("usuarios/{$user->id}/editar")
             ->put("usuarios/{$user->id}", $this->withData([
                 'state' => '',
             ]))
             ->assertRedirect("usuarios/{$user->id}/editar")
             ->assertSessionHasErrors(['state']);
 
        $this->assertDatabaseMissing('users', ['email' => 'felipe@developers.net']);
    }
    /** @test */
    function the_state_must_be_valid()
    {
        $this->handleValidationExceptions();
 
        $user = User::factory()->create();
 
        $this->from("usuarios/{$user->id}/editar")
             ->put("usuarios/{$user->id}", $this->withData([
                 'state' => 'invalid',
             ]))
             ->assertRedirect("usuarios/{$user->id}/editar")
             ->assertSessionHasErrors(['state']);
 
        $this->assertDatabaseMissing('users', ['email' => 'felipe@developers.net']);
    }
}
