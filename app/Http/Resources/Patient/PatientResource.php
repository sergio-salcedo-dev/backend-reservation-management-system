<?php

declare(strict_types=1);

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'dni' => htmlspecialchars($this->dni),
            'full_name' => htmlspecialchars($this->full_name),
            'email' => htmlspecialchars($this->email),
            'phone' => htmlspecialchars($this->phone),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
