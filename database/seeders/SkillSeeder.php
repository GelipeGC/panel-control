<?php

namespace Database\Seeders;

use App\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Skill::factory()->create(['name' => 'HTML5']);
        Skill::factory()->create(['name' => 'CSS3']);
        Skill::factory()->create(['name' => 'PHP']);
        Skill::factory()->create(['name' => 'JavaScript']);
        Skill::factory()->create(['name' => 'TDD']);
        Skill::factory()->create(['name' => 'Vuejs']);
        Skill::factory()->create(['name' => 'React']);
    }
}
