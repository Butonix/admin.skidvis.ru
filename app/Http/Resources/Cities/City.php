<?php

namespace App\Http\Resources\Cities;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class City
 * @package App\Http\Resources
 * @mixin \App\Models\Cities\City
 */
class City extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        return [
            'label'     => $this->getCityWithTime(),
            'value'     => $this->getKey(),
            'timezone'  => $this->getTimezone(),
            'utc'       => $this->getUTC(),
            'region'    => $this->getRegion(),
            'district'  => $this->getDistrict(),
            'latitude'  => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
        ];
    }
}
