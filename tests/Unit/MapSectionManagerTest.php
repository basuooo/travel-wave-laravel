<?php

namespace Tests\Unit;

use App\Support\MapSectionManager;
use Tests\TestCase;

class MapSectionManagerTest extends TestCase
{
    public function test_before_footer_position_is_available(): void
    {
        $positions = MapSectionManager::positions();

        $this->assertArrayHasKey('before_footer', $positions);
        $this->assertSame('Before footer', $positions['before_footer']);
    }
}
