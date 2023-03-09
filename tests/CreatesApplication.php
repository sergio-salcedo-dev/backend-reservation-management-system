<?php

namespace Tests;

use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    public Generator $fakerPhp;

    /** Creates the application. */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
        $this->fakerPhp = Faker::create('es_ES');

        return $app;
    }
}
