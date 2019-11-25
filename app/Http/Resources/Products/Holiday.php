<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Holiday
 * @package App\Http\Resources
 * @mixin \App\Models\Products\Holiday
 */
class Holiday extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        return [
            'id'       => $this->getKey(),
            'name'     => $this->getName(),
            'favorite' => $this->isFavorite(),
        ];
    }
}
