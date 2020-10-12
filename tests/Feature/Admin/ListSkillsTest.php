<?php

namespace Tests\Feature\Admin;

use App\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSkillsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_skills_list()
    {
        Skill::factory()->create(['name' => 'PHP']);
        Skill::factory()->create(['name' => 'TDD']);
        Skill::factory()->create(['name' => 'CSS']);

        $this->get('/habilidades/')
                ->assertStatus(200)
                ->assertSeeInOrder([
                    'CSS',
                    'PHP',
                    'TDD'
                ]);
    }
}
