<?php

namespace App\Http\Resources\Organizations;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PointCollection extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $this->collection->transform(function (Point $point) {
            return (new Point($point))->additional($this->additional);
        });

        return $this->resource;
    }
}
