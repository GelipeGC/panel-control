<?php

use App\Team;
use App\User;
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
        $admin = factory(User::class)->create([
            'team_id' => $this->teams->firstWhere('name', 'Styde'),
            'first_name' => 'Felipe ',
            'last_name' => 'Guzman',
            'email' => 'felipe@test.com',
            'password' => bcrypt('laravel'),
            'role' => 'admin',
        ]);

        $admin->skills()->attach($this->skills);

        $admin->profile()->create([
            'bio' => 'Programador, profesor, editor, social media manager',
            'profession_id' => $this->professions->where('title', 'Desarrollador back-end')->first()->id,
        ]);
    }

    protected function createRandomUser()
    {
        $user = factory(User::class)->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id,
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
    }
}
