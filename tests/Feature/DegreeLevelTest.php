<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\DegreeLevel;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DegreeLevelTest extends TestCase
{
    /** @test */
    public function admin_can_see_list_of_degree_levels()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevels = factory(DegreeLevel::class, 3)->create();
        $demRequests1 = factory(DemonstratorRequest::class, 3)->create();
        $demRequests1->each(function ($item) use ($degreeLevels) {
            $item->degreeLevels()->attach($degreeLevels[0]);
        });
        $students = factory(User::class, 5)->states('student')->create(['degree_level_id' => $degreeLevels[0]->id]);

        $response = $this->actingAs($admin)->get(route('admin.degreelevels.index'));

        $response->assertStatus(200);
        $response->assertSee($degreeLevels[0]->title);
        $response->assertSee($degreeLevels[1]->title);
        $response->assertSee($degreeLevels[2]->title);
        $response->assertSee($degreeLevels[0]->requests->count());
        $response->assertSee($degreeLevels[0]->students->count());
    }

    /** @test */
    public function admin_can_begin_to_add_a_new_degree_level()
    {
        $admin = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($admin)->get(route('admin.degreelevels.create'));

        $response->assertStatus(200);
        $response->assertSee('Degree Level');
        $response->assertSee('Save');
    }

    /** @test */
    public function admin_can_create_a_new_degree_level()
    {
        $admin = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($admin)->post(route('admin.degreelevels.store', ['title' => 'PhD']));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.degreelevels.index'));
        $response->assertSessionHas(['success_message' => 'Degree level PhD saved.']);
        $this->assertDatabaseHas('degree_levels', ['title' => 'PhD']);
    }

    /** @test */
    public function admin_cannot_create_the_same_new_degree_level_twice()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevel = factory(DegreeLevel::class)->create(['title' => 'PhD']);

        $response = $this->actingAs($admin)->post(route('admin.degreelevels.store'), ['title' => 'PhD']);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function admin_can_begin_to_edit_a_degree_level()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevel = factory(DegreeLevel::class)->create(['title' => 'PhD']);

        $response = $this->actingAs($admin)->get(route('admin.degreelevels.edit', $degreeLevel->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Degree Level');
        $response->assertSee($degreeLevel->title);
        $response->assertSee('Update');
    }

    /** @test */
    public function admin_can_update_a_degree_level()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevel = factory(DegreeLevel::class)->create(['title' => 'PhD']);

        $response = $this->actingAs($admin)->post(route('admin.degreelevels.update', $degreeLevel->id), ['title' => 'Masters']);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.degreelevels.index'));
        $response->assertSessionHas(['success_message' => 'Degree level Masters updated.']);
        $this->assertDatabaseHas('degree_levels', ['title' => 'Masters']);
        $this->assertDatabaseMissing('degree_levels', ['title' => 'PhD']);
    }

    /** @test */
    public function admin_cannot_update_a_degree_level_to_have_same_title_as_another()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevel = factory(DegreeLevel::class)->create(['title' => 'PhD']);
        $degreeLevel2 = factory(DegreeLevel::class)->create(['title' => 'Masters']);

        $response = $this->actingAs($admin)->post(route('admin.degreelevels.update', $degreeLevel->id), ['title' => 'Masters']);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('title');
        $this->assertDatabaseHas('degree_levels', ['title' => 'Masters']);
        $this->assertDatabaseHas('degree_levels', ['title' => 'PhD']);
    }

    /** @test */
    public function can_delete_a_degree_level()
    {
        $admin = factory(User::class)->states('admin')->create();
        $degreeLevel = factory(DegreeLevel::class)->create(['title' => 'PhD']);

        $response = $this->actingAs($admin)->post(route('admin.degreelevels.destroy', $degreeLevel->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.degreelevels.index'));
        $response->assertSessionHas(['success_message' => 'Deleted degree level: PhD.']);
        $this->assertDatabaseMissing('degree_levels', ['title' => 'PhD']);
    }
}
