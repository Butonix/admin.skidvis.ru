<?php

namespace App\Http\Resources\Organizations;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrganizationCollection extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return $this->resource;
    }
}
