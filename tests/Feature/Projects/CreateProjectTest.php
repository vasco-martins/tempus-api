<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateProject()
    {
        $user = $this->createUser();

        $this->assertCount(0, Project::all());

        $response = $this->actingAs($user)->post('/api/projects', [
            'name' => 'Test Project'
        ]);

        $this->assertCount(1, Project::all());
    }

    public function testProjectNameIsRequired()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/api/projects', [
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testProjectNameNeedMoreThanThreeCharacters()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/api/projects', [
            'name' => 'Ts'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testProjectNameNeedLessThanTwentyCharacters()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/api/projects', [
            'name' => 'Test project has more characters than 20 characters'
        ]);

        $response->assertSessionHasErrors(['name']);
    }


    private function createUser(): User
    {
        return User::create([
            'email' => 'user@example.com',
            'name' => 'user test',
            'password' => '12345678',
        ]);
    }
}
