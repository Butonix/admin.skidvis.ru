<?php

namespace App\Http\Resources\Reviews;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class City
 * @package App\Http\Resources
 * @mixin \App\Models\Reviews\Review
 */
class Review extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        return [
            'id'          => $this->getKey(),
            'user'        => $this->getUserShortInfo(),
            'text'        => $this->getText(),
            'pros'        => $this->getPros(),
            'cons'        => $this->getCons(),
            'rating'      => $this->getRating(),
            'likes_count' => $this->getLikesCount(),
            'liked'       => (\Auth::guard('api')->check())
                ? $this->isLiked(\Auth::guard('api')->id())
                : false,
            'is_editable' => $this->isEditable(),
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
