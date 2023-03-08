<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //dd($this->resource);
        #пропишу возвращ поля и заберу их из модели
        return [
            'group_id' => $this->group_id,
            'name' => $this->name,
            'url' => $this->url,
            'avatar' => $this->avatar,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
