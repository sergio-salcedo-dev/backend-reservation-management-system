<?php

declare(strict_types=1);

namespace App\Exceptions\Patient;

use App\Models\Patient;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PatientNotFoundException extends ModelNotFoundException
{
    protected $model = Patient::class;
}
