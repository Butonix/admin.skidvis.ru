<?php

namespace App\Http\Resources\Organizations;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class City
 * @package App\Http\Resources
 * @mixin \App\Models\Organizations\Organization
 */
class Organization extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $needShortDescription = $this->additional['meta']['needShortDescription'] ?? true;
        $needCreatedAt = $this->additional['meta']['needCreatedAt'] ?? true;
        $needCreator = $this->additional['meta']['needCreator'] ?? true;
        $needOperationMode = $this->additional['meta']['needOperationMode'] ?? true;
        $cityId = $this->additional['meta']['city_id'] ?? null;
        $filter = [];

        if (isset($cityId)) {
            $filter['city_id'] = $cityId;
        }

        $organization = [
            'id'                         => $this->getKey(),
            'name'                       => $this->getName(),
            'description'                => $this->getDescription(),
            'inn'                        => $this->getInn(),
            'link'                       => $this->getLink(),
            'rating'                     => $this->getRating(),
            'rating_user'                => $this->getRatingOfUser(),
            'reviews_count'              => $this->getReviewsCount(),
            'logo'                       => [
                'color' => $this->getAvatarColor(),
                'src'   => $this->getAvatarLink(),
                'id'    => $this->getAvatarId(),
            ],
            'mini_logo'                  => [
                'src' => $this->getMiniLogoLink(),
                'id'  => $this->getMiniLogoId(),
            ],
            'type_map_point'             => $this->getTypeMapPoint(),
            'phone'                      => $this->getPhone(),
            'email'                      => $this->getEmail(),
            'images'                     => $this->getCoversLinks(),
            'socials'                    => $this->getSocialLinks(),
            'timezone'                   => $this->getTimezoneId(),
            'left_review'                => (\Auth::guard('api')->check())
                ? $this->isUserLeftReview(\Auth::guard('api')->id())
                : false,
            'is_published'               => $this->isPublished(),
            'is_caption'                 => $this->isCaption(),
            'is_all_similar_disabled'    => $this->isAllSimilarDisabled(),
            'is_advertisement'           => $this->isAdvertisement(),
            'points_count'               => $this->getPointsCount($filter),
            'points_with_products_count' => $this->getPointsWithProductsCount($filter),
        ];

        ($needShortDescription)
            ? $organization['short_description'] = $this->getShortDescription()
            : false;
        ($needCreatedAt)
            ? $organization['created_at'] = $this->created_at->toDateTimeString()
            : false;
        ($needCreator)
            ? $organization['creator'] = $this->getCreatorName()
            : false;
        ($needOperationMode)
            ? $organization['operationMode'] = new OrganizationPointSchedule($this->getSchedule())
            : false;

        return $organization;
    }
}
