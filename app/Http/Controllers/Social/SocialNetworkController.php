<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Social;


use App\Http\Controllers\Controller;
use App\Models\Products\Tag;
use App\Models\Social\SocialNetwork;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialNetworkController extends Controller {
    /**
     * @var Tag
     */
    protected $socialNetworks;

    /**
     * TagController constructor.
     * @param Tag $tags
     */
    public function __construct(SocialNetwork $socialNetworks) {
        $this->socialNetworks = $socialNetworks;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Социальные сети');
        $frd = $request->all();
        $socialNetworks = $this->socialNetworks->filter($frd)
                                               ->orderBy('name', 'ASC')
                                               ->paginate($frd['perPage'] ?? $this->socialNetworks->getPerPage());

        return view('social.networks.index', compact('frd', 'socialNetworks'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Добавление социальной сети');
        $frd = $request->all();
        $socialNetworks = $this->socialNetworks::orderByDesc('id')->take(20)->get();

        return view('social.networks.create', compact('socialNetworks'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255|unique:social_networks',
            'display_name' => 'nullable|string|max:255',
            'link'         => 'required'
        ], [
            'name.unique' => 'Данная соц.сеть уже добавлена в систему'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $tag = $this->socialNetworks->create($frd);
        $frdSearch = [];

        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }

        $frd = $frdSearch;
        $socialNetworks = $this->socialNetworks->orderByDesc('id')->get();
        $html = view('social.networks.components._lastCreatedTags', compact('frd', 'socialNetworks'))->render();
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Соц.сеть успешно добавлена',
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
     * @param Request       $request
     * @param SocialNetwork $socialNetwork
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, SocialNetwork $socialNetwork) {
        SEOMeta::setTitle($socialNetwork->getName());
        return view('social.networks.show', compact('socialNetwork'));
    }

    /**
     * @param Request       $request
     * @param SocialNetwork $socialNetwork
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, SocialNetwork $socialNetwork) {
        SEOMeta::setTitle($socialNetwork->getName() . ' - редактирование');
        return view('social.networks.edit', compact('socialNetwork'));
    }

    /**
     * @param Request       $request
     * @param SocialNetwork $socialNetwork
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SocialNetwork $socialNetwork) {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255|unique:social_networks,name,' . $socialNetwork->getKey(),
            'display_name' => 'nullable|string|max:255',
            'link'         => 'required|'
        ], [
            'name.unique' => 'Данная соц.сеть уже добавлена в систему'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $socialNetwork->update($frd);

        $message = [
            'type' => 'success',
            'text' => 'Соц.сеть успешно обновлена'
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request       $request
     * @param SocialNetwork $socialNetwork
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, SocialNetwork $socialNetwork) {
        $socialNetwork->delete();

        $message = [
            'type' => 'success',
            'text' => 'Соц.сеть «' . $socialNetwork->getName() . '» успешно удалена'
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->only([
            'social_networks'
        ]);
        $this->socialNetworks->destroy($frd['social_networks']);
        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $socialNetworks = $this->socialNetworks->filter($frd)
                                               ->orderBy('name', 'ASC')
                                               ->paginate($frd['perPage'] ?? $this->socialNetworks->getPerPage());
        $html = view('social.networks.components._index', compact('frd', 'socialNetworks'))->render();
        $flashMessage = [
            'type'    => 'success',
            'text'    => 'Соц.сети успешно удалены',
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];
        $response = response()->json($flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allSocialNetworksJson(Request $request) {
        $socialNetworks = $this->socialNetworks::all(['icon_url'])->keyBy('link')->toArray();

        return response()->json($socialNetworks);
    }
}
