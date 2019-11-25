<?php

namespace App\Http\Resources\Organizations;

use App\Models\Organizations\Point as PointModel;
use App\Models\Organizations\Organization as OrganizationModel;
use App\Http\Resources\Products\ProductCollection;
use App\Models\Products\Category;
use App\Models\Products\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Point
 * @package App\Http\Resources
 * @mixin \App\Models\Organizations\Point
 */
class Point extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $needProducts = $this->additional['meta']['needProducts'] ?? false;
        $type = $this->additional['meta']['type'] ?? PointModel::POINTS_TYPE_DEFAULT;
        $scheduleType = $this->additional['meta']['scheduleType'] ?? PointModel::POINT_SCHEDULE_TYPE_PUBLIC;
        $filter = $this->additional['meta']['filter'] ?? [];
        $point = [];

        if ($type === PointModel::POINTS_TYPE_DEFAULT) {
            $point = [
                'id'                => $this->getKey(),
                'name'              => $this->getName(),
				'extension'              => $this->getExtension(),
                'latitude'          => $this->getLatitude(),
                'longitude'         => $this->getLongitude(),
                'street'            => $this->getStreet(),
                'building'          => $this->getBuilding(),
                'full_street'       => $this->getFullStreet(),
                'city_kladr_id'     => $this->getCityKladrId(),
                'metro_line_color'     => $this->getColorMetroStations(),
                'metro_distance'     => $this->getMetroStationDistance(),
                'metro_station_name'     => $this->getNameMetroStation(),
                'metro_line_name'     => $this->getNameMetroLine(),
                'city'              => [
                    'id'        => $this->getCityId(),
                    'name'      => $this->getCityName(),
                    'latitude'  => $this->getCityLatitude(),
                    'longitude' => $this->getCityLongitude(),
                ],
                'timezone'          => $this->getTimezone(),
                'payload'           => $this->getPayload(),
                'phone'             => $this->getPhone(),
                'email'             => $this->getEmail(),
                'own_schedule'      => $this->hasOwnSchedule(),
                'operationModeText' => $this->getScheduleText(),
                'operationMode'     => new OrganizationPointSchedule($this->getSchedule($scheduleType)),
            ];
        } elseif ($type === PointModel::POINTS_TYPE_FOR_MAP) {
            $point = [
                'id'          => $this->getKey(),
                'name'        => $this->getName(),
                'color'        => null,
                'latitude'    => $this->getLatitude(),
                'longitude'   => $this->getLongitude(),
				'street'      => $this->getStreet(),
                'full_street' => $this->getFullStreet(),
            ];
        }


        if ($needProducts) {
            $hasPointImage = false;
            Product::productsByPoint($this->getKey())
                   ->publicProducts()
                   ->filter($filter)
                   ->ordering($filter)
                   ->chunk(100, function ($products) use (&$point, &$hasPointImage) {
                       foreach ($products as $product) {
                           /**
                            * @var Product $product
                            */
                           [$iconImage, $iconImageType, $color] = $product->getIconImageForMap();

                           if (!is_null($iconImage)) {
                               $hasPointImage = true;
                               $point['color'] = $color;
                               $point['img'] = $iconImage;
                               $point['type_point_map'] = $iconImageType;
                               break;
                           }
                       }

                       $point['products'] = (new ProductCollection($products))->additional([
                           'meta' => [
                               'type'          => Product::PRODUCTS_TYPE_FOR_MAP,
                               'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
                           ],
                       ]);
                   });

            if (!$hasPointImage) {
                $point['img'] = Category::getDefaultImageForMap();
                $point['type_point_map'] = OrganizationModel::TYPE_MAP_POINT_CATEGORY;
            }

            //$products = $this->products()->filter($filter)->ordering($filter)->get();
            //$point['products'] = (new ProductCollection($products))->additional([
            //    'meta' => [
            //        'type' => Product::PRODUCTS_TYPE_FOR_MAP,
            //        'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
            //    ],
            //]);
        }

        return $point;
    }
}
