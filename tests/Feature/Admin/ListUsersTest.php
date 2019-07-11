<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(User::class)->create([
            'name' => 'Joel'
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee(trans('users.title.index'))
            ->assertSee('Joel')
            ->assertSee('Ellie');

        $this->assertNotRepeatedQueries();
    }

    /** @test */
    function it_paginates_the_users()
    {
        factory(User::class)->create([
            'name' => 'Tercer Usuario',
            'created_at' => now()->subDays(5),
        ]);

        factory(User::class)->times(12)->create([
            'created_at' => now()->subDays(4),
        ]);

        factory(User::class)->create([
            'name' => 'Decimoséptimo Usuario',
            'created_at' => now()->subDays(2),
        ]);

        factory(User::class)->create([
            'name' => 'Segundo Usuario',
            'created_at' => now()->subDays(6),
        ]);

        factory(User::class)->create([
            'name' => 'Primer Usuario',
            'created_at' => now()->subWeek(),
        ]);

        factory(User::class)->create([
            'name' => 'Decimosexto Usuario',
            'created_at' => now()->subDays(3),
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Decimoséptimo Usuario',
                'Decimosexto Usuario',
                'Tercer Usuario',
            ])
            ->assertDontSee('Segundo Usuario')
            ->assertDontSee('Primer Usuario');

        $this->get('/usuarios?page=2')
            ->assertSeeInOrder([
                'Segundo Usuario',
                'Primer Usuario',
            ])
            ->assertDontSee('Tercer Usuario');
    }

    /** @test */
    function users_are_ordered_by_name()
    {
        factory(User::class)->create(['name' => 'John Doe']);
        factory(User::class)->create(['name' => 'Richard Roe']);
        factory(User::class)->create(['name' => 'Jane Doe']);

        $this->get('/usuarios?order=name&direction=asc')
            ->assertSeeInOrder([
                'Jane Doe',
                'John Doe',
                'Richard Roe'
            ]);

        $this->get('/usuarios?order=name&direction=desc')
            ->assertSeeInOrder([
                'Richard Roe',
                'John Doe',
                'Jane Doe',
            ]);

    }

    /** @test */
    function users_are_ordered_by_email()
    {
        factory(User::class)->create(['email' => 'john.doe@example.com']);
        factory(User::class)->create(['email' => 'richard.roe@example.com']);
        factory(User::class)->create(['email' => 'jane.doe@example.com']);

        $this->get('/usuarios?order=email&direction=asc')
            ->assertSeeInOrder([
                'jane.doe@example.com',
                'john.doe@example.com',
                'richard.roe@example.com'
            ]);

        $this->get('/usuarios?order=email&direction=desc')
            ->assertSeeInOrder([
                'richard.roe@example.com',
                'john.doe@example.com',
                'jane.doe@example.com',
            ]);

    }
    /** @test */
    function users_are_ordered_by_registration_date()
    {   
        factory(User::class)->create(['name' => 'John Doe', 'created_at' => now()->subDays(1)]);
        factory(User::class)->create(['name' => 'Richard Roe', 'created_at' => now()->subDays(2)]);
        factory(User::class)->create(['name' => 'Jane Doe', 'created_at' => now()->subDays(3)]);


        $this->get('/usuarios?order=created_at&direction=asc')
            ->assertSeeInOrder([
                'Jane Doe',
                'Richard Roe',
                'John Doe',
            ]);

        $this->get('/usuarios?order=created_at&direction=desc')
            ->assertSeeInOrder([
                'John Doe',
                'Richard Roe',
                'Jane Doe',
            ]);

    }
    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_shows_the_deleted_users()
    {
        factory(User::class)->create([
            'name' => 'Joel',
            'deleted_at' => now(),
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios/papelera')
            ->assertStatus(200)
            ->assertSee(trans('users.title.trash'))
            ->assertSee('Joel')
            ->assertDontSee('Ellie');
    }
}
