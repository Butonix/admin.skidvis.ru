<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class User
 * @package App\Http\Resources
 * @mixin \App\Models\Users\User
 */
class User extends JsonResource {
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
            'f_name'   => $this->getFirstName(),
            'l_name'   => $this->getLastName(),
            'm_name'   => $this->getMiddleName(),
            'email'    => $this->getEmail(),
            'phone'    => $this->getPhone(),
            'roles'    => $this->getRoles(),
            'initials' => $this->getInitials(),
            'avatar'   => [
                'id'  => $this->getAvatarId(),
                'src' => $this->getAvatarLink(),
            ],
            'city'     => [
                'id'        => $this->getCityId(),
                'name'      => $this->getCityName(),
                'latitude'  => $this->getCityLatitude(),
                'longitude' => $this->getCityLongitude(),
            ],
            'wishlist' => $this->getBookmarksProductsIds(),
            'bookmarks' => $this->getBookmarksArticlesIds()
        ];
    }
}
