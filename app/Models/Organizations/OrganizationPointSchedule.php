<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 25.06.2019
 * Time: 14:28
 */

namespace App\Models\Organizations;


use App\Models\Cities\City;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Organizations\OrganizationPointSchedule
 *
 * @property int                                                                                                 $id
 * @property string|null                                                                                         $mon_start
 * @property string|null                                                                                         $mon_end
 * @property string|null                                                                                         $tue_start
 * @property string|null                                                                                         $tue_end
 * @property string|null                                                                                         $wed_start
 * @property string|null                                                                                         $wed_end
 * @property string|null                                                                                         $thu_start
 * @property string|null                                                                                         $thu_end
 * @property string|null                                                                                         $fri_start
 * @property string|null                                                                                         $fri_end
 * @property string|null                                                                                         $sat_start
 * @property string|null                                                                                         $sat_end
 * @property string|null                                                                                         $sun_start
 * @property string|null                                                                                         $sun_end
 * @property int                                                                                                 $mon_active
 * @property int                                                                                                 $tue_active
 * @property int                                                                                                 $wed_active
 * @property int                                                                                                 $thu_active
 * @property int                                                                                                 $fri_active
 * @property int                                                                                                 $sat_active
 * @property int                                                                                                 $sun_active
 * @property string|null                                                                                         $scheduleable_id
 * @property string|null                                                                                         $scheduleable_type
 * @property \Illuminate\Support\Carbon|null                                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                                     $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\OrganizationPointSchedule[] $scheduleable
 * @property string|null                                                                                         $text_time
 * @property int|null                                                                                            $timezone_id
 * @property int                                                                                                 $is_different
 * @property-read \App\Models\Cities\City                                                                        $city
 * @property int                                                                                                 $type
 * @property \Illuminate\Support\Carbon|null                                                                     $deleted_at
 * @property int|null                                                                                            $city_id
 * @property-read \App\Models\Cities\City|null                                                                   $timezone
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereFriEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereFriStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereMonEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereMonStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSatEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSatStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereScheduleableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereScheduleableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSunEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSunStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereThuEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereThuStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereTueEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereTueStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereWedEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereWedStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereFriActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereMonActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSatActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereSunActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereThuActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereTueActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereWedActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereIsDifferent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\OrganizationPointSchedule whereTextTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\OrganizationPointSchedule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\OrganizationPointSchedule withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\OrganizationPointSchedule onlyTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class OrganizationPointSchedule extends Model {
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'mon_start',
        'mon_end',
        'mon_active',
        'tue_start',
        'tue_end',
        'tue_active',
        'wed_start',
        'wed_end',
        'wed_active',
        'thu_start',
        'thu_end',
        'thu_active',
        'fri_start',
        'fri_end',
        'fri_active',
        'sat_start',
        'sat_end',
        'sat_active',
        'sun_start',
        'sun_end',
        'sun_active',
        'scheduleable_id',
        'scheduleable_type',
        'city_id',
        'is_different', //Если расписание принадлежит точке и оно отличается от расписания организации => true
        'text_time', //Текст расписания по дням
    ];

    /**
     * @var string
     */
    protected $table = 'organization_point_schedule';

    /**
     * @return null|string
     */
    public function getMonStart(): ?string {
        $monStart = $this->mon_start;

        if (is_null($monStart)) {
            return null;
        }

        $monStart = Carbon::createFromTimeString($monStart)->format('H:i');
        return $monStart;
    }

    /**
     * @param null|string $mon_start
     */
    public function setMonStart(?string $mon_start): void {
        $this->mon_start = $mon_start;
    }

    /**
     * @return null|string
     */
    public function getMonEnd(): ?string {
        $monEnd = $this->mon_end;

        if (is_null($monEnd)) {
            return null;
        }

        $monEnd = Carbon::createFromTimeString($monEnd)->format('H:i');
        return $monEnd;
    }

    /**
     * @param null|string $mon_end
     */
    public function setMonEnd(?string $mon_end): void {
        $this->mon_end = $mon_end;
    }

    /**
     * @return null|string
     */
    public function getTueStart(): ?string {
        $tueStart = $this->tue_start;

        if (is_null($tueStart)) {
            return null;
        }

        $tueStart = Carbon::createFromTimeString($tueStart)->format('H:i');
        return $tueStart;
    }

    /**
     * @param null|string $tue_start
     */
    public function setTueStart(?string $tue_start): void {
        $this->tue_start = $tue_start;
    }

    /**
     * @return null|string
     */
    public function getTueEnd(): ?string {
        $tueEnd = $this->tue_end;

        if (is_null($tueEnd)) {
            return null;
        }

        $tueEnd = Carbon::createFromTimeString($tueEnd)->format('H:i');
        return $tueEnd;
    }

    /**
     * @param null|string $tue_end
     */
    public function setTueEnd(?string $tue_end): void {
        $this->tue_end = $tue_end;
    }

    /**
     * @return null|string
     */
    public function getWedStart(): ?string {
        $wedStart = $this->wed_start;

        if (is_null($wedStart)) {
            return null;
        }

        $wedStart = Carbon::createFromTimeString($wedStart)->format('H:i');
        return $wedStart;
    }

    /**
     * @param null|string $wed_start
     */
    public function setWedStart(?string $wed_start): void {
        $this->wed_start = $wed_start;
    }

    /**
     * @return null|string
     */
    public function getWedEnd(): ?string {
        $wedEnd = $this->wed_end;

        if (is_null($wedEnd)) {
            return null;
        }

        $wedEnd = Carbon::createFromTimeString($wedEnd)->format('H:i');
        return $wedEnd;
    }

    /**
     * @param null|string $wed_end
     */
    public function setWedEnd(?string $wed_end): void {
        $this->wed_end = $wed_end;
    }

    /**
     * @return null|string
     */
    public function getThuStart(): ?string {
        $thuStart = $this->thu_start;

        if (is_null($thuStart)) {
            return null;
        }

        $thuStart = Carbon::createFromTimeString($thuStart)->format('H:i');
        return $thuStart;
    }

    /**
     * @param null|string $thu_start
     */
    public function setThuStart(?string $thu_start): void {
        $this->thu_start = $thu_start;
    }

    /**
     * @return null|string
     */
    public function getThuEnd(): ?string {
        $thuEnd = $this->thu_end;

        if (is_null($thuEnd)) {
            return null;
        }

        $thuEnd = Carbon::createFromTimeString($thuEnd)->format('H:i');
        return $thuEnd;
    }

    /**
     * @param null|string $thu_end
     */
    public function setThuEnd(?string $thu_end): void {
        $this->thu_end = $thu_end;
    }

    /**
     * @return null|string
     */
    public function getFriStart(): ?string {
        $friStart = $this->fri_start;

        if (is_null($friStart)) {
            return null;
        }

        $friStart = Carbon::createFromTimeString($friStart)->format('H:i');
        return $friStart;
    }

    /**
     * @param null|string $fri_start
     */
    public function setFriStart(?string $fri_start): void {
        $this->fri_start = $fri_start;
    }

    /**
     * @return null|string
     */
    public function getFriEnd(): ?string {
        $friEnd = $this->fri_end;

        if (is_null($friEnd)) {
            return null;
        }

        $friEnd = Carbon::createFromTimeString($friEnd)->format('H:i');
        return $friEnd;
    }

    /**
     * @param null|string $fri_end
     */
    public function setFriEnd(?string $fri_end): void {
        $this->fri_end = $fri_end;
    }

    /**
     * @return null|string
     */
    public function getSatStart(): ?string {
        $satStart = $this->sat_start;

        if (is_null($satStart)) {
            return null;
        }

        $satStart = Carbon::createFromTimeString($satStart)->format('H:i');
        return $satStart;
    }

    /**
     * @param null|string $sat_start
     */
    public function setSatStart(?string $sat_start): void {
        $this->sat_start = $sat_start;
    }

    /**
     * @return null|string
     */
    public function getSatEnd(): ?string {
        $satEnd = $this->sat_end;

        if (is_null($satEnd)) {
            return null;
        }

        $satEnd = Carbon::createFromTimeString($satEnd)->format('H:i');
        return $satEnd;
    }

    /**
     * @param null|string $sat_end
     */
    public function setSatEnd(?string $sat_end): void {
        $this->sat_end = $sat_end;
    }

    /**
     * @return null|string
     */
    public function getSunStart(): ?string {
        $sunStart = $this->sun_start;

        if (is_null($sunStart)) {
            return null;
        }

        $sunStart = Carbon::createFromTimeString($sunStart)->format('H:i');
        return $sunStart;
    }

    /**
     * @param null|string $sun_start
     */
    public function setSunStart(?string $sun_start): void {
        $this->sun_start = $sun_start;
    }

    /**
     * @return null|string
     */
    public function getSunEnd(): ?string {
        $sunEnd = $this->sun_end;

        if (is_null($sunEnd)) {
            return null;
        }

        $sunEnd = Carbon::createFromTimeString($sunEnd)->format('H:i');
        return $sunEnd;
    }

    /**
     * @param null|string $sun_end
     */
    public function setSunEnd(?string $sun_end): void {
        $this->sun_end = $sun_end;
    }

    /**
     * @return int
     */
    public function getMonActive(): int {
        return $this->mon_active;
    }

    /**
     * @param int $mon_active
     */
    public function setMonActive(int $mon_active): void {
        $this->mon_active = $mon_active;
    }

    /**
     * @return int
     */
    public function getTueActive(): int {
        return $this->tue_active;
    }

    /**
     * @param int $tue_active
     */
    public function setTueActive(int $tue_active): void {
        $this->tue_active = $tue_active;
    }

    /**
     * @return int
     */
    public function getWedActive(): int {
        return $this->wed_active;
    }

    /**
     * @param int $wed_active
     */
    public function setWedActive(int $wed_active): void {
        $this->wed_active = $wed_active;
    }

    /**
     * @return int
     */
    public function getThuActive(): int {
        return $this->thu_active;
    }

    /**
     * @param int $thu_active
     */
    public function setThuActive(int $thu_active): void {
        $this->thu_active = $thu_active;
    }

    /**
     * @return int
     */
    public function getFriActive(): int {
        return $this->fri_active;
    }

    /**
     * @param int $fri_active
     */
    public function setFriActive(int $fri_active): void {
        $this->fri_active = $fri_active;
    }

    /**
     * @return int
     */
    public function getSatActive(): int {
        return $this->sat_active;
    }

    /**
     * @param int $sat_active
     */
    public function setSatActive(int $sat_active): void {
        $this->sat_active = $sat_active;
    }

    /**
     * @return int
     */
    public function getSunActive(): int {
        return $this->sun_active;
    }

    /**
     * @param int $sun_active
     */
    public function setSunActive(int $sun_active): void {
        $this->sun_active = $sun_active;
    }

    /**
     * @return MorphTo
     */
    public function scheduleable(): MorphTo {
        return $this->morphTo();
    }

    /**
     * @return array
     */
    public function getSchedule(): array {
        $result = [];

        return $result;
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City {
        return $this->city;
    }

    /**
     * @return bool
     */
    public function hasTimezone(): bool {
        return (!is_null($this->getCity()));
    }

    /**
     * @return int|null
     */
    public function getTimezoneId(): ?int {
        if (!$this->hasTimezone()) {
            return null;
        }

        return $this->getCity()->getKey();
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function getTimeWithoutSeconds(string $time): string {
        $time = Carbon::createFromTimeString($time)->format('H:i');
        return $time;
    }

    /**
     * @return null|string
     */
    public function getTextTime(): ?string {
        return $this->{'text_time'};
    }

    /**
     * @param null|string $textTime
     */
    public function setTextTime(?string $textTime): void {
        $this->{'text_time'} = $textTime;
    }

    /**
     * @return null|string
     */
    public function generateTextTime(): ?string {
        $days = [
            'mon' => 'ПН',
            'tue' => 'ВТ',
            'wed' => 'СР',
            'thu' => 'ЧТ',
            'fri' => 'ПТ',
            'sat' => 'СБ',
            'sun' => 'ВС',
        ];
        $result = [];

        $daysRangeStart = null;
        $daysRangeEnd = null;
        $daysTime = null;
        foreach ($days as $dayEn => $dayRu) {
            if ($dayEn === 'mon' || is_null($daysRangeStart)) {
                $dayStart = $this->{$dayEn . '_start'};
                $dayEnd = $this->{$dayEn . '_end'};
                $dayActive = $this->{$dayEn . '_active'};

                if (isset($dayStart) && isset($dayEnd) && $dayActive) {
                    $daysRangeStart = $dayRu;
                    $daysTime = $this->getTimeWithoutSeconds($dayStart) . '-' . $this->getTimeWithoutSeconds($dayEnd);
                }

                if ($dayEn !== 'sun' || ($dayEn === 'sun' && $daysRangeStart === null)) {
                    continue;
                }
            }

            $dayStart = $this->{$dayEn . '_start'};
            $dayEnd = $this->{$dayEn . '_end'};
            $dayActive = $this->{$dayEn . '_active'};

            if (isset($dayStart) && isset($dayEnd) && $dayActive && ($dayRu !== $daysRangeStart)) {
                $daysTimeTemp = $this->getTimeWithoutSeconds($dayStart) . '-' . $this->getTimeWithoutSeconds($dayEnd);

                if ($daysTime === $daysTimeTemp) {
                    $daysRangeEnd = $dayRu;
                } else {
                    $resultTemp = (isset($daysRangeEnd))
                        ? $daysRangeStart . '-' . $daysRangeEnd
                        : $daysRangeStart;
                    $resultTemp .= ' ' . $daysTime;
                    $result[] = $this->firstLetterToUpper($resultTemp);
                    $daysRangeStart = $dayRu;
                    $daysTime = $daysTimeTemp;
                    $daysRangeEnd = null;
                }
            } elseif (isset($dayStart) && isset($dayEnd) && !$dayActive && ($dayRu !== $daysRangeStart)) {
                $resultTemp = (isset($daysRangeEnd))
                    ? $daysRangeStart . '-' . $daysRangeEnd
                    : $daysRangeStart;
                $resultTemp .= ' ' . $daysTime;
                $result[] = $this->firstLetterToUpper($resultTemp);
                $daysRangeStart = null;
                $daysTime = null;
                $daysRangeEnd = null;
                continue;
            }

            if ($dayEn === 'sun') {
                $resultTemp = (isset($daysRangeEnd))
                    ? $daysRangeStart . '-' . $daysRangeEnd
                    : $daysRangeStart;
                $resultTemp .= ' ' . $daysTime;
                $result[] = $this->firstLetterToUpper($resultTemp);
                $daysRangeEnd = null;
                continue;
            }
        }

        return implode(', ', $result);
    }

    /**
     *
     */
    public function updateTextTime(): void {
        $textTime = $this->generateTextTime();
        $this->setTextTime($textTime);
    }

    /**
     * @return string
     */
    //public function getTimeText(): string {
    //    $days = [
    //        'mon' => 'ПН',
    //        'tue' => 'ВТ',
    //        'wed' => 'СР',
    //        'thu' => 'ЧТ',
    //        'fri' => 'ПТ',
    //        'sat' => 'СБ',
    //        'sun' => 'ВС',
    //    ];
    //    $result = [];
    //
    //    $daysRangeStart = null;
    //    $daysRangeEnd = null;
    //    $daysTime = null;
    //    foreach ($days as $dayEn => $dayRu) {
    //        if ($dayEn === 'mon' || is_null($daysRangeStart)) {
    //            $dayStart = $this->{$dayEn . '_start'};
    //            $dayEnd = $this->{$dayEn . '_end'};
    //            $dayActive = $this->{$dayEn . '_active'};
    //
    //            if (isset($dayStart) && isset($dayEnd) && $dayActive) {
    //                $daysRangeStart = $dayRu;
    //                $daysTime = $this->getTimeWithoutSeconds($dayStart) . '-' . $this->getTimeWithoutSeconds($dayEnd);
    //            }
    //
    //            if ($dayEn !== 'sun' || ($dayEn === 'sun' && $daysRangeStart === null)) {
    //                continue;
    //            }
    //        }
    //
    //        $dayStart = $this->{$dayEn . '_start'};
    //        $dayEnd = $this->{$dayEn . '_end'};
    //        $dayActive = $this->{$dayEn . '_active'};
    //
    //        if (isset($dayStart) && isset($dayEnd) && $dayActive && ($dayRu !== $daysRangeStart )) {
    //            $daysTimeTemp = $this->getTimeWithoutSeconds($dayStart) . '-' . $this->getTimeWithoutSeconds($dayEnd);
    //
    //            if ($daysTime === $daysTimeTemp) {
    //                $daysRangeEnd = $dayRu;
    //            } else {
    //                $resultTemp = (isset($daysRangeEnd))
    //                    ? $daysRangeStart . '-' . $daysRangeEnd
    //                    : $daysRangeStart;
    //                $resultTemp .= ' ' . $daysTime;
    //                $result[] = $this->firstLetterToUpper($resultTemp);
    //                $daysRangeStart = $dayRu;
    //                $daysTime = $daysTimeTemp;
    //                $daysRangeEnd = null;
    //            }
    //        }
    //        elseif (isset($dayStart) && isset($dayEnd) && !$dayActive && ($dayRu !== $daysRangeStart )) {
    //            $resultTemp = (isset($daysRangeEnd))
    //                ? $daysRangeStart . '-' . $daysRangeEnd
    //                : $daysRangeStart;
    //            $resultTemp .= ' ' . $daysTime;
    //            $result[] = $this->firstLetterToUpper($resultTemp);
    //            $daysRangeStart = null;
    //            $daysTime = null;
    //            $daysRangeEnd = null;
    //            continue;
    //        }
    //
    //        if ($dayEn === 'sun') {
    //            $resultTemp = (isset($daysRangeEnd))
    //                ? $daysRangeStart . '-' . $daysRangeEnd
    //                : $daysRangeStart;
    //            $resultTemp .= ' ' . $daysTime;
    //            $result[] = $this->firstLetterToUpper($resultTemp);
    //            $daysRangeEnd = null;
    //            continue;
    //        }
    //    }
    //
    //    return implode(', ', $result);
    //}

    /**
     * @param string $text
     *
     * @return string
     */
    private function firstLetterToUpper(string $text): string {
        $text = mb_strtolower($text);
        $text = mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);

        return $text;
    }

    /**
     * @return bool
     */
    public function isDifferent(): bool {
        return $this->{'is_different'};
    }

    /**
     * @param bool $isDifferent
     */
    public function setIsDifferent(bool $isDifferent): void {
        $this->{'is_different'} = $isDifferent;
    }

    /**
     * @param OrganizationPointSchedule $firstSchedule
     * @param OrganizationPointSchedule $secondSchedule
     *
     * @return bool
     */
    public static function isSchedulesEqual(OrganizationPointSchedule $firstSchedule, OrganizationPointSchedule $secondSchedule): bool {
        $firstScheduleTimeText = $firstSchedule->getTextTime();
        $secondScheduleTimeText = $secondSchedule->getTextTime();

        return ($firstScheduleTimeText === $secondScheduleTimeText);
    }
}
