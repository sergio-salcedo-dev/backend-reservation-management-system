<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Patient
 *
 * @property int $id
 * @property string $dni
 * @property string $full_name
 * @property string $email
 * @property int $phone
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Appointment[] $appointments
 */
class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'full_name',
        'email',
        'phone',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
