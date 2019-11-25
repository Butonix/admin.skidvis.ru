<?php

namespace App\Http\Resources\Users;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection {
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
