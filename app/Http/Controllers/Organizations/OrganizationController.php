<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 18:59
 */

namespace App\Http\Controllers\Organizations;


use App\Http\Controllers\Controller;
use App\Models\Communications\Email;
use App\Models\Communications\Phone;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Social\SocialAccount;
use App\Models\Social\SocialNetwork;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller {
    /**
     * @var Organization
     */
    protected $organizations;

    /**
     * @var SocialNetwork
     */
    protected $socialNetworks;

    /**
     * OrganizationController constructor.
     *
     * @param Organization $organizations
     */
    public function __construct(Organization $organizations, SocialNetwork $socialNetworks) {
        $this->organizations = $organizations;
        $this->socialNetworks = $socialNetworks;

        $this->middleware(['permission:organizations--create'])->only(['create', 'store']);
        $this->middleware(['permission:organizations--read'])->only(['index', 'show']);
        $this->middleware(['permission:organizations--update'])->only(['edit', 'update']);
        $this->middleware(['permission:organizations--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Организации');
        $frd = $request->all();
        $user = \Auth::user();

        if (isset($frd['points'])) {
            $frd['orderingDir'] = $frd['points'];
        } elseif (isset($frd['products'])) {
            $frd['orderingDir'] = $frd['products'];
        } else {
            $frd['orderingDir'] = 'ASC';
        }

        if ($user->isSuperAdministrator()) {
            $organizations = $this->organizations::with(['products', 'points', 'avatar', 'phone', 'email'])
                                                 ->filter($frd)
                                                 ->ordering($frd)
                                                 ->orderBy('name', 'ASC')
                                                 ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());
        } elseif ($user->isAdministrator() || $user->isManager()) {
            $organizations = $this->organizations::organizationsByUser($user->getKey())
                                                 ->with([
                                                     'products',
                                                     'points',
                                                     'avatar',
                                                     'phone',
                                                     'email',
                                                 ])
                                                 ->filter($frd)
                                                 ->ordering($frd)
                                                 ->orderBy('name', 'ASC')
                                                 ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());
        } else {
            $organizations = [];
        }

        return view('organizations.index', compact('frd', 'organizations'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание организации');
        $socialNetworks = $this->socialNetworks::all();
        $schedule = (new Organization())->getScheduleEmpty();
        $coversLinks = [];
        $coversCount = 0;

        return view('organizations.create', compact('socialNetworks', 'schedule', 'coversCount', 'coversLinks'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), Organization::getRules(), Organization::getMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $payload = [
            'orgnip'    => $frd['orgnip'],
            'okved'     => $frd['okved'],
            'address'   => $frd['address'],
            'latitude'  => $frd['latitude'],
            'longitude' => $frd['longitude'],
        ];
        $organization = $this->organizations->create($frd);
        $organization->setPayload($payload);
        $organization->save();

        $organization->updatePhone($frd['phone'] ?? null);
        $organization->save();

        $organization->updateEmail($frd['email'] ?? null);
        $organization->save();

        if (!is_null($frd['avatar'])) {
            $file = $frd['avatar'];
            $avatar = new Image();
            $avatar->download($file);
            $avatar = $organization->images()->save($avatar);
            $organization->avatar()->associate($avatar);
        }

        $organization->updateSliderImages($frd['images']);
        $organization->save();

        $orgSchedule = $organization->getSchedule();
        if (isset($frd['operationMode']) && !empty($frd['operationMode'])) {
            if (is_null($orgSchedule)) {
                $orgSchedule = new OrganizationPointSchedule();
            }

            foreach ($frd['operationMode'] as $day => $schedule) {
                $active = (isset($schedule['active']) && $schedule['active'] === 'on')
                    ? true
                    : false;
                $orgSchedule->{$day . '_start'} = $schedule['start'];
                $orgSchedule->{$day . '_end'} = $schedule['end'];
                $orgSchedule->{$day . '_active'} = $active;
            }

            if (isset($frd['timezone'])) {
                $orgSchedule->city()->associate($frd['timezone']);
            }

            $organization->schedule()->save($orgSchedule);
        } else {
            if (!is_null($orgSchedule)) {
                $orgSchedule->delete();
            }
        }
        //$organization->updateSchedule($frd['operationMode'] ?? [], $frd['timezone'] ?? null);
        $organization->save();

        //foreach ($frd['social_networks'] as $socialNetworkId => $socialNetworkLink) {
        //    if (is_null($socialNetworkLink)) {
        //        continue;
        //    }
        //
        //    $socialAccount = $organization->socialAccounts()->save(new SocialAccount([
        //        'link'              => $socialNetworkLink,
        //        'social_network_id' => $socialNetworkId,
        //    ]));
        //}

        $organization->creator()->associate(\Auth::user());
        $organization->save();
        $message = [
            'type' => 'success',
            'text' => 'Организация успешно создана',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Organization $organization) {
        $user = \Auth::user();
        $organizations = $this->organizations::organizationsByUser($user->getKey());

        //Проверка если пользователь администратор\менеджер и организация, которую он открывает, ему не принадлежит,
        //то у него недостаточно прав для просмотра
        if (($user->isAdministrator() || $user->isManager()) && ($organizations->whereKey($organization->getKey())->doesntExist())) {
            abort(403);
        }

        SEOMeta::setTitle($organization->getName() ?? 'Организация');
        $socialAccounts = $organization->getSocialAccounts();
        $coversLinks = $organization->getCoversLinks();
        $coversCount = $organization->countCovers();
        $schedule = $organization->getScheduleSimple();

        return view('organizations.show', compact('organization', 'socialAccounts', 'coversLinks', 'coversCount', 'schedule'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Organization $organization) {
        $user = \Auth::user();
        $organizations = $this->organizations::organizationsByUser($user->getKey());

        //Проверка если пользователь администратор\менеджер и организация, которую он открывает, ему не принадлежит,
        //то у него недостаточно прав для редактирования
        if (($user->isAdministrator() || $user->isManager()) && ($organizations->whereKey($organization->getKey())->doesntExist())) {
            abort(403);
        }

        SEOMeta::setTitle(($organization->getName())
            ? $organization->getName() . ' - редактирование'
            : 'Редактирование организации');
        $coversLinks = $organization->getCoversLinks();
        $coversCount = $organization->countCovers();
        $schedule = $organization->getScheduleSimple();

        return view('organizations.edit', compact('organization', 'coversLinks', 'coversCount', 'schedule'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function update(Request $request, Organization $organization) {
        $validator = Validator::make($request->all(), Organization::getRules(), Organization::getMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $payload = [
            'orgnip'    => $frd['orgnip'],
            'okved'     => $frd['okved'],
            'address'   => $frd['address'],
            'latitude'  => $frd['latitude'],
            'longitude' => $frd['longitude'],
        ];
        $organization->update($frd);
        $organization->setPayload($payload);
        $organization->save();

        $organization->updatePhone($frd['phone'] ?? null);
        $organization->save();

        $organization->updateEmail($frd['email'] ?? null);
        $organization->save();

        if (!is_null($frd['avatar'])) {
            $file = $frd['avatar'];
            $oldAvatar = $organization->getAvatar();

            if (!is_null($oldAvatar)) {
                $oldAvatar->forceDelete();
                $oldAvatar->delete();
            }

            $avatar = new Image();
            $avatar->download($file);
            $avatar = $organization->images()->save($avatar);
            $organization->avatar()->associate($avatar);
        }

        $organization->updateSliderImages($frd['images']);
        $organization->save();

        $orgSchedule = $organization->getSchedule();
        if (isset($frd['operationMode']) && !empty($frd['operationMode'])) {
            if (is_null($orgSchedule)) {
                $orgSchedule = new OrganizationPointSchedule();
            }

            foreach ($frd['operationMode'] as $day => $schedule) {
                $active = (isset($schedule['active']) && $schedule['active'] === 'on')
                    ? true
                    : false;
                $orgSchedule->{$day . '_start'} = $schedule['start'];
                $orgSchedule->{$day . '_end'} = $schedule['end'];
                $orgSchedule->{$day . '_active'} = $active;
            }

            if (isset($frd['timezone'])) {
                $orgSchedule->city()->associate($frd['timezone']);
            }

            $organization->schedule()->save($orgSchedule);
        } else {
            if (!is_null($orgSchedule)) {
                $orgSchedule->delete();
            }
        }
        //$organization->updateSchedule($frd['operationMode'] ?? [], $frd['timezone'] ?? null);
        $organization->save();

        $organization->save();
        $message = [
            'type' => 'success',
            'text' => 'Организация успешно обновлена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Organization $organization) {
        $organization->delete();

        $message = [
            'type' => 'success',
            'text' => 'Организация «' . $organization->getName() . '» успешно удалена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }
}
