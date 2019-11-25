<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 14.06.2019
 * Time: 11:21
 */

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Models\Users\Auth\AuthProvider;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthProviderController extends Controller {
    /**
     * @var AuthProvider
     */
    protected $providers;

    /**
     * AuthProviderController constructor.
     * @param AuthProvider $authProvider
     */
    public function __construct(AuthProvider $authProvider) {
        $this->providers = $authProvider;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Провайдеры аутентификации');

        $frd = $request->all();

        $providers = $this->providers->filter($frd)
                                     ->orderBy('name', 'ASC')
                                     ->paginate($frd['perPage'] ?? $this->providers->getPerPage());

        return view('auth.providers', compact('frd', 'providers'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name'          => ['required', 'string', 'max:255'],
            'icon_url'      => ['string', 'max:255'],
            'slug'          => ['required', 'string', 'max:255'],
            'client_id'     => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255']
        ]);
        $frd = $request->only(['name', 'slug', 'icon_url', 'client_id', 'client_secret']);
        $frd['slug'] = mb_strtolower($frd['slug']);
        $provider = $this->providers->create($frd);

        $clientIdConfigKey = 'services.' . $frd['slug'] . '.client_id';
        $clientSecretConfigKey = 'services.' . $frd['slug'] . '.client_secret';
        $redirectConfigKey = 'services.' . $frd['slug'] . '.redirect';

        $provider->setPayload('client_id', [
            'config_key'   => $clientIdConfigKey,
            'config_value' => $frd['client_id']
        ]);
        $provider->setPayload('client_secret', [
            'config_key'   => $clientSecretConfigKey,
            'config_value' => $frd['client_secret']
        ]);
        $provider->setPayload('redirect', [
            'config_key'   => $redirectConfigKey,
            'config_value' => route('auth.social.callback', $provider)
        ]);
        $provider->save();

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $providers = $this->providers->filter($frd)
                                     ->orderBy('name', 'ASC')
                                     ->paginate($frd['perPage'] ?? $this->providers->getPerPage());
        $html = view('auth.components._providers', compact('providers', 'frd'))->render();

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Сервис «' . $provider->getName() . '» успешно создан',
            ],
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];

        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request      $request
     * @param AuthProvider $provider
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, AuthProvider $provider) {
        SEOMeta::setTitle($provider->getName() . ' - редактирование');

        $clientId = $provider->getClientIdConfig();
        $clientSecret = $provider->getClientSecretConfig();

        return view('auth.providersEdit', compact('provider', 'clientId', 'clientSecret'));
    }

    /**
     * @param Request      $request
     * @param AuthProvider $provider
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, AuthProvider $provider) {
        $this->validate($request, [
            'name'          => ['required', 'string', 'max:255'],
            'icon_url'      => ['string', 'max:255'],
            'client_id'     => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255']
        ]);
        $frd = $request->only(['name', 'icon_url', 'client_id', 'client_secret']);
        $provider->update($frd);

        $clientIdConfigKey = 'services.' . $provider->getSlug() . '.client_id';
        $clientSecretConfigKey = 'services.' . $provider->getSlug() . '.client_secret';

        $provider->setPayload('client_id', [
            'config_key'   => $clientIdConfigKey,
            'config_value' => $frd['client_id']
        ]);
        $provider->setPayload('redirect', [
            'config_key'   => $clientSecretConfigKey,
            'config_value' => $frd['client_secret']
        ]);

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Сервис «' . $provider->getName() . '» успешно обновлен',
            ]
        ];

        return response()->json($message, 200);
    }

    /**
     * @param Request      $request
     * @param AuthProvider $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPublished(Request $request, AuthProvider $provider) {
        $frd = $request->only(['published']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['published']) && 'on' === $frd['published']) {
            $provider->setPublished();
            $provider->save();
            $message['message']['text'] = 'Сервис активирован';
        } else {
            $provider->unsetPublished();
            $provider->save();
            $message['message']['text'] = 'Сервис деактивирован';
        }

        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->only([
            'providers'
        ]);
        $this->providers->destroy($frd['providers']);
        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $providers = $this->providers->filter($frd)
                                     ->orderBy('name', 'ASC')
                                     ->paginate($frd['perPage'] ?? $this->providers->getPerPage());
        $html = view('auth.components._providers', compact('providers', 'frd'))->render();
        $flashMessage = [
            'type'    => 'success',
            'text'    => 'Сервисы успешно удалены.',
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];
        $response = response()->json($flashMessage);

        return $response;
    }
}
