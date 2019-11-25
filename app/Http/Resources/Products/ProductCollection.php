<?php

namespace App\Http\Resources\Products;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection {
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function toArray($request) {
        $this->collection->transform(function (Product $product) {
            return (new Product($product))->additional($this->additional);
        });

        return $this->resource;
    }
}
