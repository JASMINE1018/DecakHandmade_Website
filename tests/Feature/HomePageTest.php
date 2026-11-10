<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_the_landing_page(): void
    {
        $this->get('/home')
            ->assertOk()
            ->assertSee('An Aesthetic Touch')
            ->assertSee('Discover Your Favorite Works')
            ->assertSee('Explore Decak Categories')
            ->assertSee('Chat with us!');
    }
}
