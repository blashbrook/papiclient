<?php

namespace Blashbrook\PAPIClient\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    #[Test]
    public function it_can_run_basic_test()
    {
        $this->assertTrue(true);
    }
}
