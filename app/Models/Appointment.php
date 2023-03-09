<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Appointment
 *
 * @property int $id
 * @property int $patient_id
 * @property string $type
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Patient $patient
 */
class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'start_date',
        'end_date',
        'type',
    ];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
