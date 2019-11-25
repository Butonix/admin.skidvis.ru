<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 31.07.2019
 * Time: 16:09
 */

namespace App\Traits;


use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Organizations\Point;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait SchedulesTrait
 * @property-read \App\Models\Organizations\OrganizationPointSchedule $schedule
 * @package App\Traits
 * @mixin \Eloquent
 */
trait SchedulesTrait {
    /**
     * @return MorphOne
     */
    public function schedule(): MorphOne {
        return $this->morphOne(OrganizationPointSchedule::class, 'scheduleable');
    }

    /**
     * @return OrganizationPointSchedule|null
     */
    public function getSchedule(): ?OrganizationPointSchedule {
        return $this->schedule;
    }

    /**
     * @return array
     */
    public function getScheduleEmpty(): array {
        return [
            'mon' => [
                'nameRus' => 'Понедельник',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'tue' => [
                'nameRus' => 'Вторник',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'wed' => [
                'nameRus' => 'Среда',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'thu' => [
                'nameRus' => 'Четверг',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'fri' => [
                'nameRus' => 'Пятница',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'sat' => [
                'nameRus' => 'Суббота',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
            'sun' => [
                'nameRus' => 'Воскресенье',
                'start'  => null,
                'end'    => null,
                'active' => null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getScheduleSimple(): array {
        $schedule = $this->getSchedule();
        return [
            'mon' => [
                'nameRus' => 'Понедельник',
                'start'  => $schedule->getMonStart(),
                'end'    => $schedule->getMonEnd(),
                'active' => $schedule->getMonActive(),
            ],
            'tue' => [
                'nameRus' => 'Вторник',
                'start'  => $schedule->getTueStart(),
                'end'    => $schedule->getTueEnd(),
                'active' => $schedule->getTueActive(),
            ],
            'wed' => [
                'nameRus' => 'Среда',
                'start'  => $schedule->getWedStart(),
                'end'    => $schedule->getWedEnd(),
                'active' => $schedule->getWedActive(),
            ],
            'thu' => [
                'nameRus' => 'Четверг',
                'start'  => $schedule->getThuStart(),
                'end'    => $schedule->getThuEnd(),
                'active' => $schedule->getThuActive(),
            ],
            'fri' => [
                'nameRus' => 'Пятница',
                'start'  => $schedule->getFriStart(),
                'end'    => $schedule->getFriEnd(),
                'active' => $schedule->getFriActive(),
            ],
            'sat' => [
                'nameRus' => 'Суббота',
                'start'  => $schedule->getSatStart(),
                'end'    => $schedule->getSatEnd(),
                'active' => $schedule->getSatActive(),
            ],
            'sun' => [
                'nameRus' => 'Воскресенье',
                'start'  => $schedule->getSunStart(),
                'end'    => $schedule->getSunEnd(),
                'active' => $schedule->getSunActive(),
            ],
        ];
    }

    /**
     * @return bool
     */
    public function hasSchedule(): bool {
        return !is_null($this->getSchedule()); //Если null, то расписание для заведения отсутствует, иначе расписание есть
    }

    /**
     * @param array             $newSchedule
     * @param bool              $ownSchedule
     * @param int|null          $timezone
     * @param Organization|null $organization
     */
    public function updateSchedule(array $newSchedule, bool $ownSchedule, ?int $timezone, ?Organization $organization): void {
        if (is_null($organization)) {
            //Если $organization === null, то значит, что расписание устанавливается для организации
            $oldSchedule = $this->getSchedule();
        } else {
            //Если $organization !== null, то значит, что расписание устанавливается адреса
            $oldSchedule = $this->getSchedule(Point::POINT_SCHEDULE_TYPE_OWN);
        }

        if (!empty($newSchedule)) {
            if (is_null($oldSchedule)) {
                $oldSchedule = new OrganizationPointSchedule();
            }

            foreach ($newSchedule as $day => $schedule) {
                $oldSchedule->{$day . '_start'} = $schedule['start'];
                $oldSchedule->{$day . '_end'} = $schedule['end'];
                $oldSchedule->{$day . '_active'} = $schedule['active'];
            }

            if (!is_null($organization)) {
                $this->setOwnSchedule($ownSchedule);

                if (!OrganizationPointSchedule::isSchedulesEqual($organization->getSchedule(), $oldSchedule)) {
                    $oldSchedule->setIsDifferent(true);
                }
            }

            if (!is_null($timezone)) {
                $oldSchedule->city()->associate($timezone);
            }

            $oldSchedule->updateTextTime();
            $this->schedule()->save($oldSchedule);
        } else {
            if (!is_null($organization)) {
                $this->setOwnSchedule(false);
            }
        }
    }
}
