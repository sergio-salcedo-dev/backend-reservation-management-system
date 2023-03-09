<?php

declare(strict_types=1);

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
//            'patient' => new PatientResource($this->whenLoaded('patient')),
            'appointmentType' => htmlspecialchars($this->type),
            'dayOfWeek' => $this->start_date?->format('l'),
            'date' => $this->start_date?->format('Y-m-d'),
            'time' => $this->start_date?->format('H:i'),
            'duration' => $this->start_date->diffInHours($this->end_date),
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
