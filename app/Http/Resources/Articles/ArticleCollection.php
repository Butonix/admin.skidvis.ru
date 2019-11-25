<?php

namespace App\Http\Resources\Articles;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $this->collection->transform(function (Article $article) {
            return (new Article($article))->additional($this->additional);
        });

        return $this->resource;
    }
}
