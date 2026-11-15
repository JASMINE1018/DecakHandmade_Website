<?php

namespace Tests\Feature;

use Tests\TestCase;

class StoryPageTest extends TestCase
{
    public function test_story_page_renders_the_react_root(): void
    {
        $this->get(route('story.index'))
            ->assertOk()
            ->assertSee('<title>Cerita Kami — Decak Handmade</title>', false)
            ->assertSee('id="our-story-root"', false);
    }
}
