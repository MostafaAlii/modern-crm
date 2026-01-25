<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->resource['code'],
            'name' => $this->resource['name'],
            'native' => $this->resource['native'],
            'script' => $this->resource['script'],
            'regional' => $this->resource['regional'],
            'status' => $this->resource['status'],
            'is_active' => $this->resource['is_active'],
        ];
    }
}
