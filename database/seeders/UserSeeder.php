<?php

namespace Database\Seeders;

use App\Team;
use App\User;
use App\Login;
use App\Skill;
use App\Profession;
use App\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    protected $professions;
    protected $skills;
    protected $teams;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fetchRelations();

        
        $this->createAdmin();

        foreach (range(1, 99) as $i) {
            $this->createRandomUser();
        }
    }

    protected function fetchRelations()
    {
        $this->professions = Profession::all();

        $this->skills = Skill::all();

        $this->teams = Team::all();
    }

    protected function createAdmin()
    {
        $admin = User::factory()->create([
            'team_id' => $this->teams->firstWhere('name', 'Styde'),
            'name' => 'Felipe Guzman',
            'email' => 'felipe@test.com',
            'password' => bcrypt('laravel'),
            'role' => 'admin',
            'active' => true
        ]);

        $admin->skills()->attach($this->skills);

        $admin->profile->update([
            'bio' => 'Programador, profesor, editor, social media manager',
            'profession_id' => $this->professions->where('title', 'Desarrollador back-end')->first()->id,
        ]);
    }

    protected function createRandomUser()
    {
        $user = User::factory()->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id,
            'active' => rand(0, 3) ? true : false,
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        $user->profile->update([

            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
        
        Login::factory()->times(rand(1, 10))->create([
            'user_id' => $user->id,
        ]);
    }
}
