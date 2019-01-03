<?php

namespace Tests\Browser\Admin;

use App\{Profession, Skill, User};
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_user_can_be_created()
    {
        $profession =  factory(Profession::class)->create();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $this->browse(function (Browser $browser) use($profession, $skillA, $skillB) {
            $browser->visit('/usuarios/nuevo')
                    ->type('name', 'Felipe')
                    ->type('email','felipe@developers.net')
                    ->type('password','secret')
                    ->type('bio', 'Programador')
                    ->select('profession_id', $profession->id)
                    ->type('twitter', 'https://twitter.com/gelipeGC')
                    ->check("skills[{$skillA->id}]")
                    ->check("skills[{$skillB->id}]")
                    ->radio('role', 'user')
                    ->press('Crear usuario')
                    ->assertPathIs('/usuarios')
                    ->assertSee('Felipe')
                    ->assertSee('felipe@developers.net');
        });

        $this->assertCredentials([
            'name' => 'Felipe',
            'email' => 'felipe@developers.net',
            'password' => 'secret',
            'role'  => 'user'
        ]);

        $user = User::findByEmail('felipe@developers.net');

        $this->assertDatabaseHas('user_profiles',[
            'bio'   => 'Programador',
            'twitter' => 'https://twitter.com/gelipeGC',
            'user_id' => $user->id,
            'profession_id' => $profession->id,

        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id
        ]);
    }
}
