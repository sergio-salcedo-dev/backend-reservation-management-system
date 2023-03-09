<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class AppEnvironmentHelper
{
    public static function isLocalOrTestingEnvironment(): bool
    {
        $environments = ['local', 'testing'];

        return in_array(App::environment(), $environments);
    }
}
