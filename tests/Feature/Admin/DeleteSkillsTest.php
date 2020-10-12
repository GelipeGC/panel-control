<?php

namespace Tests\Feature\Admin;

use App\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSkillsTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function it_delete_a_skill()
    {
        $skill = Skill::factory()->create();

        $response = $this->delete("habilidades/{$skill->id}");

        $response->assertRedirect();

        $this->assertDatabaseEmpty('skills');
    }
}
