<?php

declare(strict_types=1);

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        self::withoutWrapping();

        $key = $this->resource['key'] ?? 'message';
        $message = $this->resource['message'] ?? '';

        return [
            'message' => $message,
            'errors' => [$key => $message],
        ];
    }
}
