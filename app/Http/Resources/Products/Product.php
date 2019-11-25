<?php

namespace App\Http\Resources\Products;

use App\Models\Products\Product as ProductModel;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Products\Category as CategoryResource;
use App\Models\Products\Category;

/**
 * Class Product
 * @package App\Http\Resources
 * @mixin \App\Models\Products\Product
 */
class Product extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $type = $this->additional['meta']['type'] ?? ProductModel::PRODUCTS_TYPE_DEFAULT;
        $typeOfPoints = $this->additional['meta']['typeOfPoints'] ?? ProductModel::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE;
        $pointsFilter = $this->additional['meta']['pointsFilter'] ?? [];
        $typeOfPublish = $this->additional['meta']['typeOfPublish'] ?? ProductModel::PRODUCT_PUBLISH_INDIVIDUAL;
        $product = [];

        if ($type === ProductModel::PRODUCTS_TYPE_DEFAULT) {
            $product = [
                'id'                 => $this->getKey(),
                'name'               => $this->getName(),
                'caption'               => $this->getCaption(),
                'description'        => $this->getDescription(),
                'short_description'  => $this->getShortDescription(),
                'conditions'         => $this->getConditions(),
                'created_at'         => (!is_null($this->created_at))
                    ? $this->created_at->toDateTimeString()
                    : null,
                'start_at'           => (!is_null($this->getStartAt()))
                    ? $this->getStartAt()->toDateString()
                    : $this->getStartAt(),
                'end_at'             => (!is_null($this->getEndAt()))
                    ? $this->getEndAt()->toDateString()
                    : $this->getEndAt(),
                'origin_price'       => $this->getOriginPrice(),
                'value'              => $this->getValue(),
                'currency_id'        => $this->getCurrencyId(),
                'main_category_id'        => $this->getMainCategoryId(),
                'creator'            => $this->getCreatorName(),
                'images'             => $this->getImagesLinks(),
                'categories'         => $this->getCategoriesShortInfo(),
                'tags'               => $this->getTagsShortInfo(),
                'auditories'         => $this->getAuditoriesShortInfo(),
                'holidays'           => $this->getHolidaysShortInfo(),
                'points'             => $this->getPointsByType($typeOfPoints, $pointsFilter),
                'organization_id'    => $this->getOrganizationId(),
                'organization_logo'  => $this->getOrganizationAvatarLink(),
                'organization_color' => $this->getOrganizationAvatarColor(),
				'organization_link'  => $this->getOrganizationLink(),
				'organization_is_caption'  => $this->getOrganizationIsCaption(),
				'phone'    => $this->getOrganizationPhone(),
				'email'    => $this->getOrganizationEmail(),
				'is_all_similar'  => $this->isAllSimilar(),
				'is_advertisement'  => $this->getIsAdvertisement(),
				'is_perpetual'  => $this->getIsPerpetual(),
                'socials'            => $this->getSocialLinks(),
                'left_review'        => (\Auth::guard('api')->check())
                    ? $this->isUserLeftReview(\Auth::guard('api')->id())
                    : false,
                'operationModeText'  => $this->getScheduleText(),
                'is_published'       => $this->getPublishedByType($typeOfPublish),
                'views'              => $this->getViews(),
            ];
        } elseif ($type === ProductModel::PRODUCTS_TYPE_FOR_MAP) {
            $product = [
                'id'                 => $this->getKey(),
                'name'               => $this->getName(),
                'shortDescription'               => $this->getShortDescription(),
                'origin_price'       => $this->getOriginPrice(),
                'category'           => (new CategoryResource($this->getFirstCategoryWithImage()))->additional([
                    'meta' => [
                        'type' => Category::CATEGORIES_FOR_MAP,
                    ],
                ]),
                'value'              => $this->getValue(),
                'currency_id'        => $this->getCurrencyId(),
                'organization_logo'  => $this->getOrganizationAvatarLink(),
                'organization_color' => $this->getOrganizationAvatarColor(),
            ];
        }

        return $product;
    }

    /**
     * @param int   $typeOfPoints
     * @param array $filter
     *
     * @return array
     */
    private function getPointsByType(int $typeOfPoints, array $filter): array {
        if ($typeOfPoints === ProductModel::POINTS_IDS_TYPE) {
            return $this->getPointsIds($filter);
        } elseif ($typeOfPoints === ProductModel::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE) {
            return $this->getPointsForPublicProductsIndex($filter);
        } elseif ($typeOfPoints === ProductModel::POINTS_FOR_PUBLIC_PRODUCT_SHOW) {
            return $this->getPointsForPublicProductShow($filter);
        }

        return [];
    }

    /**
     * @param int $typeOfPublish
     *
     * @return bool|null
     */
    private function getPublishedByType(int $typeOfPublish): ?bool {
        if ($typeOfPublish === ProductModel::PRODUCT_PUBLISH_INDIVIDUAL) {
            return $this->getIsPublished();
        } else { //иначе ProductModel::PRODUCT_PUBLISH_WITH_ORGANIZATION
            return $this->isPublished();
        }
    }
}
