<?php

namespace App\Http\Resources\Organizations;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OrganizationPointSchedule
 * @package App\Http\Resources
 * @mixin \App\Models\Organizations\OrganizationPointSchedule
 */
class OrganizationPointSchedule extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'mon' => [
                'start'  => $this->getMonStart(),
                'end'    => $this->getMonEnd(),
                'active' => $this->getMonActive(),
            ],
            'tue' => [
                'start'  => $this->getTueStart(),
                'end'    => $this->getTueEnd(),
                'active' => $this->getTueActive(),
            ],
            'wed' => [
                'start'  => $this->getWedStart(),
                'end'    => $this->getWedEnd(),
                'active' => $this->getWedActive(),
            ],
            'thu' => [
                'start'  => $this->getThuStart(),
                'end'    => $this->getThuEnd(),
                'active' => $this->getThuActive(),
            ],
            'fri' => [
                'start'  => $this->getFriStart(),
                'end'    => $this->getFriEnd(),
                'active' => $this->getFriActive(),
            ],
            'sat' => [
                'start'  => $this->getSatStart(),
                'end'    => $this->getSatEnd(),
                'active' => $this->getSatActive(),
            ],
            'sun' => [
                'start'  => $this->getSunStart(),
                'end'    => $this->getSunEnd(),
                'active' => $this->getSunActive(),
            ]
        ];
    }
}
