<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitLocalhostTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_localhost_redirect_to_spa_url(): void
    {
        $this->get('/')->assertMovedPermanently();
    }
}
