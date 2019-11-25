<?php

namespace App\Http\Resources\Cities;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CityCollection extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'default' => \App\Models\Cities\City::DEFAULT_TIMEZONE_ID,
            'data'    => $this->resource
        ];
    }
}
