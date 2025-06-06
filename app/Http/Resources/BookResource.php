<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'status' => $this->status->value,
            'price' => $this->price,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
