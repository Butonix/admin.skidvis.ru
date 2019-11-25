<?php

namespace App\Http\Resources\Products;

use App\Models\Products\Category as CategoryModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Category
 * @package App\Http\Resources
 * @mixin \App\Models\Products\Category
 */
class Category extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $type = $this->additional['meta']['type'] ?? CategoryModel::CATEGORIES_DEFAULT;
        $category = [];

        if ($type === CategoryModel::CATEGORIES_DEFAULT) {
            $category = [
                'id'       => $this->getKey(),
                'name'     => $this->getName(),
                'favorite' => $this->isFavorite(),
                'images'   => [
                    'default'  => [
                        'normal' => $this->getImageLink(),
                        'active' => $this->getActiveImageLink(),
                    ],
                    'business' => [
                        'normal' => $this->getBusinessImageLink(),
                        'active' => $this->getBusinessActiveImageLink(),
                    ],
                ],
            ];
        } elseif ($type === CategoryModel::CATEGORIES_FOR_MAP) {
            $category = [
                'id'    => $this->getKey(),
                'color'    => $this->getColor(),
                'image' => $this->getEmptyImageLink(),
            ];
        }

        return $category;
    }
}
