<?php

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
        factory(\App\Skill::class)->create(['name' => 'HTML5']);
        factory(\App\Skill::class)->create(['name' => 'CSS3']);
        factory(\App\Skill::class)->create(['name' => 'PHP']);
        factory(\App\Skill::class)->create(['name' => 'JavaScript']);
        factory(\App\Skill::class)->create(['name' => 'TDD']);
    }
}
