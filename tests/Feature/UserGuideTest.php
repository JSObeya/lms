<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_guide_page_is_publicly_accessible(): void
    {
        $response = $this->get('/user-guide');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Trainee/Guide'));
    }

    public function test_trainee_guide_alias_is_accessible(): void
    {
        $response = $this->get('/trainee-guide');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Trainee/Guide'));
    }

    public function test_guide_redirects_to_user_guide(): void
    {
        $response = $this->get('/guide');
        $response->assertRedirect('/user-guide');
    }
}
