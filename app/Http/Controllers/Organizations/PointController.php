<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 24.06.2019
 * Time: 14:45
 */

namespace App\Http\Controllers\Organizations;


use App\Http\Controllers\Controller;
use App\Models\Communications\Email;
use App\Models\Communications\Phone;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Organizations\Point;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller {
    /**
     * @var Point
     */
    protected $points;

    /**
     * PointController constructor.
     *
     * @param Point $points
     */
    public function __construct(Point $points) {
        $this->points = $points;

        $this->middleware(['permission:points--create'])->only(['create', 'store']);
        $this->middleware(['permission:points--read'])->only(['index', 'show']);
        $this->middleware(['permission:points--update'])->only(['edit', 'update']);
        $this->middleware(['permission:points--delete'])->only(['destroy']);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Organization $organization) {
        SEOMeta::setTitle('Точки ' . $organization->getName());
        $frd = $request->all();
        $points = $organization->points()
                               ->filter($frd)
                               ->with(['phone', 'email'])
                               ->orderBy('name', 'ASC')
                               ->paginate($frd['perPage'] ?? $this->points->getPerPage());

        return view('organizations.points.index', compact('frd', 'points', 'organization'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Organization $organization) {
        SEOMeta::setTitle('Создание точки');
        return view('organizations.points.create', compact('organization'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Organization $organization) {
        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), [
            'name'             => 'string|max:255',
            'latitude'         => 'nullable|string',
            'longitude'        => 'nullable|string',
            'full_street'      => 'nullable|string|max:255',
            'street'           => 'nullable|string',
            'building'         => 'nullable|string',
            'phone'            => 'nullable|string|min:10|max:10',
            'email'            => 'nullable|string',
            'inherit_schedule' => 'string',
            'schedule_type'    => 'string',
            'weekdays_start'   => 'nullable|string',
            'weekdays_end'     => 'nullable|string',
            'weekends_start'   => 'nullable|string',
            'weekends_end'     => 'nullable|string',
            'mon_start'        => 'nullable|string',
            'mon_end'          => 'nullable|string',
            'tue_start'        => 'nullable|string',
            'tue_end'          => 'nullable|string',
            'wed_start'        => 'nullable|string',
            'wed_end'          => 'nullable|string',
            'thu_start'        => 'nullable|string',
            'thu_end'          => 'nullable|string',
            'fri_start'        => 'nullable|string',
            'fri_end'          => 'nullable|string',
            'sat_start'        => 'nullable|string',
            'sat_end'          => 'nullable|string',
            'sun_start'        => 'nullable|string',
            'sun_end'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $point = $organization->points()->save(new Point($frd));

        if (isset($frd['phone'])) {
            $phone = $point->phones()->save(new Phone(['phone' => $frd['phone']]));
            $point->phone()->associate($phone);
        }

        if (isset($frd['email'])) {
            $email = $point->emails()->save(new Email(['email' => $frd['email']]));
            $point->email()->associate($email);
        }

        if ($frd['inherit_schedule'] === 'own_schedule') {
            if ($frd['schedule_type'] === 'weekdays_weekends') {
                $schedule = new OrganizationPointSchedule([
                    'type'      => 0,
                    'mon_start' => $frd['weekdays_start'],
                    'mon_end'   => $frd['weekdays_end'],
                    'tue_start' => $frd['weekdays_start'],
                    'tue_end'   => $frd['weekdays_end'],
                    'wed_start' => $frd['weekdays_start'],
                    'wed_end'   => $frd['weekdays_end'],
                    'thu_start' => $frd['weekdays_start'],
                    'thu_end'   => $frd['weekdays_end'],
                    'fri_start' => $frd['weekdays_start'],
                    'fri_end'   => $frd['weekdays_end'],
                    'sat_start' => $frd['weekends_start'],
                    'sat_end'   => $frd['weekends_end'],
                    'sun_start' => $frd['weekends_start'],
                    'sun_end'   => $frd['weekends_end'],
                ]);
            } elseif ($frd['schedule_type'] === 'singly_day') {
                $schedule = new OrganizationPointSchedule([
                    'type'      => 1,
                    'mon_start' => $frd['mon_start'],
                    'mon_end'   => $frd['mon_end'],
                    'tue_start' => $frd['tue_start'],
                    'tue_end'   => $frd['tue_end'],
                    'wed_start' => $frd['wed_start'],
                    'wed_end'   => $frd['wed_end'],
                    'thu_start' => $frd['thu_start'],
                    'thu_end'   => $frd['thu_end'],
                    'fri_start' => $frd['fri_start'],
                    'fri_end'   => $frd['fri_end'],
                    'sat_start' => $frd['sat_start'],
                    'sat_end'   => $frd['sat_end'],
                    'sun_start' => $frd['sun_start'],
                    'sun_end'   => $frd['sun_end'],
                ]);
            }
            $point->schedules()->save($schedule);
        }

        $point->save();
        $message = [
            'type' => 'success',
            'text' => 'Точка успешно создана',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Organization $organization, Point $point) {
        SEOMeta::setTitle($point->getName() ?? 'Точка');
        return view('organizations.points.show', compact('organization', 'point'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Organization $organization, Point $point) {
        SEOMeta::setTitle(($point->getName())
            ? $point->getName() . ' - редактирование'
            : 'Редактирование точки');
        return view('organizations.points.edit', compact('organization', 'point'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, Organization $organization, Point $point) {
        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), [
            'name'             => 'string|max:255',
            'latitude'         => 'nullable|string',
            'longitude'        => 'nullable|string',
            'full_street'      => 'nullable|string|max:255',
            'street'           => 'nullable|string',
            'building'         => 'nullable|string',
            'phone'            => 'nullable|string|min:10|max:10',
            'email'            => 'nullable|string',
            'inherit_schedule' => 'string',
            'schedule_type'    => 'string',
            'weekdays_start'   => 'nullable|string',
            'weekdays_end'     => 'nullable|string',
            'weekends_start'   => 'nullable|string',
            'weekends_end'     => 'nullable|string',
            'mon_start'        => 'nullable|string',
            'mon_end'          => 'nullable|string',
            'tue_start'        => 'nullable|string',
            'tue_end'          => 'nullable|string',
            'wed_start'        => 'nullable|string',
            'wed_end'          => 'nullable|string',
            'thu_start'        => 'nullable|string',
            'thu_end'          => 'nullable|string',
            'fri_start'        => 'nullable|string',
            'fri_end'          => 'nullable|string',
            'sat_start'        => 'nullable|string',
            'sat_end'          => 'nullable|string',
            'sun_start'        => 'nullable|string',
            'sun_end'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $frd['latitude'] = (float)$frd['latitude'];
        $frd['longitude'] = (float)$frd['longitude'];
        $point->update($frd);

        $phone = $point->phone;
        if (isset($frd['phone'])) {
            if (!is_null($phone)) {
                $phone->update(['phone' => $frd['phone']]);
            } else {
                $phone = $point->phones()->save(new Phone(['phone' => $frd['phone']]));
                $point->phone()->associate($phone);
            }
        } else {
            if (!is_null($phone)) {
                $phone->delete();
            }
            $point->phone()->dissociate();
        }

        $email = $point->email;
        if (isset($frd['email'])) {
            if (!is_null($email)) {
                $email->update(['email' => $frd['email']]);
            } else {
                $email = $point->emails()->save(new Email(['email' => $frd['email']]));
                $point->email()->associate($email);
            }
        } else {
            if (!is_null($email)) {
                $email->delete();
            }
            $point->email()->dissociate();
        }

        if ($frd['inherit_schedule'] === 'own_schedule' && false === $point->hasOwnSchedule()) {
            if ($frd['schedule_type'] === 'weekdays_weekends') {
                $schedule = new OrganizationPointSchedule([
                    'type'      => 0,
                    'mon_start' => $frd['weekdays_start'],
                    'mon_end'   => $frd['weekdays_end'],
                    'tue_start' => $frd['weekdays_start'],
                    'tue_end'   => $frd['weekdays_end'],
                    'wed_start' => $frd['weekdays_start'],
                    'wed_end'   => $frd['weekdays_end'],
                    'thu_start' => $frd['weekdays_start'],
                    'thu_end'   => $frd['weekdays_end'],
                    'fri_start' => $frd['weekdays_start'],
                    'fri_end'   => $frd['weekdays_end'],
                    'sat_start' => $frd['weekends_start'],
                    'sat_end'   => $frd['weekends_end'],
                    'sun_start' => $frd['weekends_start'],
                    'sun_end'   => $frd['weekends_end'],
                ]);
            } elseif ($frd['schedule_type'] === 'singly_day') {
                $schedule = new OrganizationPointSchedule([
                    'type'      => 1,
                    'mon_start' => $frd['mon_start'],
                    'mon_end'   => $frd['mon_end'],
                    'tue_start' => $frd['tue_start'],
                    'tue_end'   => $frd['tue_end'],
                    'wed_start' => $frd['wed_start'],
                    'wed_end'   => $frd['wed_end'],
                    'thu_start' => $frd['thu_start'],
                    'thu_end'   => $frd['thu_end'],
                    'fri_start' => $frd['fri_start'],
                    'fri_end'   => $frd['fri_end'],
                    'sat_start' => $frd['sat_start'],
                    'sat_end'   => $frd['sat_end'],
                    'sun_start' => $frd['sun_start'],
                    'sun_end'   => $frd['sun_end'],
                ]);
            }
            $point->schedules()->save($schedule);
        } elseif ($frd['inherit_schedule'] === 'own_schedule' && true === $point->hasOwnSchedule()) {
            $needNewSchedule = false;
            if ($frd['schedule_type'] === 'weekdays_weekends') { //Расписание если будни/выходные
                $schedule = $point->getSchedule();

                if ($point->getScheduleType()) { // Если расписание на каждый день
                    $schedule->delete();
                    $needNewSchedule = true;
                } else {
                    if ($schedule->getMonStart() !== $frd['weekdays_start'] || $schedule->getMonEnd() !== $frd['weekdays_end'] || $schedule->getSatStart() !== $frd['weekends_start'] || $schedule->getSatEnd() !== $frd['weekends_end']) {
                        $schedule->delete();
                        $needNewSchedule = true;
                    }
                }

                if ($needNewSchedule) {
                    $schedule = new OrganizationPointSchedule([
                        'type'      => 0,
                        'mon_start' => $frd['weekdays_start'],
                        'mon_end'   => $frd['weekdays_end'],
                        'tue_start' => $frd['weekdays_start'],
                        'tue_end'   => $frd['weekdays_end'],
                        'wed_start' => $frd['weekdays_start'],
                        'wed_end'   => $frd['weekdays_end'],
                        'thu_start' => $frd['weekdays_start'],
                        'thu_end'   => $frd['weekdays_end'],
                        'fri_start' => $frd['weekdays_start'],
                        'fri_end'   => $frd['weekdays_end'],
                        'sat_start' => $frd['weekends_start'],
                        'sat_end'   => $frd['weekends_end'],
                        'sun_start' => $frd['weekends_start'],
                        'sun_end'   => $frd['weekends_end'],
                    ]);
                    $point->schedules()->save($schedule);
                }
            } elseif ($frd['schedule_type'] === 'singly_day') { //Иначе если на каждый день
                $schedule = $point->getSchedule();

                if ($point->getScheduleType() === 0) { // Если расписание будни/выходные
                    $schedule->delete();
                    $needNewSchedule = true;
                } else {
                    if ($schedule->getMonStart() !== $frd['mon_start'] || $schedule->getMonEnd() !== $frd['mon_end'] || $schedule->getTueStart() !== $frd['tue_start'] || $schedule->getTueEnd() !== $frd['tue_end'] || $schedule->getWedStart() !== $frd['wed_start'] || $schedule->getWedEnd() !== $frd['wed_end'] || $schedule->getThuStart() !== $frd['thu_start'] || $schedule->getThuEnd() !== $frd['thu_end'] || $schedule->getFriStart() !== $frd['fri_start'] || $schedule->getFriEnd() !== $frd['fri_end'] || $schedule->getSatStart() !== $frd['sat_start'] || $schedule->getSatEnd() !== $frd['sat_end'] || $schedule->getSunStart() !== $frd['sun_start'] || $schedule->getSunEnd() !== $frd['sun_end']) {
                        $schedule->delete();
                        $needNewSchedule = true;
                    }
                }

                if ($needNewSchedule) {
                    $schedule = new OrganizationPointSchedule([
                        'type'      => 1,
                        'mon_start' => $frd['mon_start'],
                        'mon_end'   => $frd['mon_end'],
                        'tue_start' => $frd['tue_start'],
                        'tue_end'   => $frd['tue_end'],
                        'wed_start' => $frd['wed_start'],
                        'wed_end'   => $frd['wed_end'],
                        'thu_start' => $frd['thu_start'],
                        'thu_end'   => $frd['thu_end'],
                        'fri_start' => $frd['fri_start'],
                        'fri_end'   => $frd['fri_end'],
                        'sat_start' => $frd['sat_start'],
                        'sat_end'   => $frd['sat_end'],
                        'sun_start' => $frd['sun_start'],
                        'sun_end'   => $frd['sun_end'],
                    ]);
                    $point->schedules()->save($schedule);
                }
            }
        } elseif ($frd['inherit_schedule'] === 'organization_schedule' && true === $point->hasOwnSchedule()) {
            $schedule = $point->getSchedule();
            $schedule->delete();
        } elseif ($frd['inherit_schedule'] === 'organization_schedule' && false === $point->hasOwnSchedule()) {
            // Пустое тело if'a для наглядности того, что ничего не происходит,
            // если выбран тип "Расписание как у организации" и у точки уже был такой тип ранее
        }

        $point->save();
        $message = [
            'type' => 'success',
            'text' => 'Точка успешно обновлена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Organization $organization, Point $point) {
        $point->delete();

        $message = [
            'type' => 'success',
            'text' => 'Точка «' . $point->getName() . '» успешно удалена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }
}
