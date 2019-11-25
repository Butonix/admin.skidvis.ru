<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Products;


use App\Http\Controllers\Controller;
use App\Models\Products\Auditory;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuditoryController extends Controller {
    /**
     * @var Auditory
     */
    protected $auditories;

    /**
     * AuditoryController constructor.
     *
     * @param Auditory $auditories
     */
    public function __construct(Auditory $auditories) {
        $this->auditories = $auditories;

        $this->middleware(['permission:auditories--create'])->only(['create', 'store']);
        $this->middleware(['permission:auditories--read'])->only(['index', 'show']);
        $this->middleware(['permission:auditories--update'])->only(['edit', 'update', 'favorite']);
        $this->middleware(['permission:auditories--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Аудитория');
        $frd = $request->all();
        $auditories = $this->auditories->filter($frd)
                                       ->ordering($frd)
                                       ->paginate($frd['perPage'] ?? $this->auditories->getPerPageForAdminPanel());

        return view('products.auditories.index', compact('frd', 'auditories'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание аудитории');
        $frd = $request->all();
        $auditories = $this->auditories::orderByDesc('id')->take(20)->get();

        return view('products.auditories.create', compact('auditories'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:auditories',
        ], [
            'name.unique' => 'Данная аудитория уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $auditory = $this->auditories->create($frd);
        $frdSearch = [];

        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }

        $frd = $frdSearch;
        $auditories = $this->auditories->orderByDesc('id')->get();
        $html = view('products.auditories.components._lastCreatedAuditories', compact('frd', 'auditories'))->render();
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Аудитория успешно добавлена.',
            ],
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html,
            ],
        ];
        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request  $request
     * @param Auditory $auditory
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Auditory $auditory) {
        SEOMeta::setTitle($auditory->getName());
        return view('products.auditories.show', compact('auditory'));
    }

    /**
     * @param Request  $request
     * @param Auditory $auditory
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Auditory $auditory) {
        SEOMeta::setTitle($auditory->getName() . ' - редактирование');
        return view('products.auditories.edit', compact('auditory'));
    }

    /**
     * @param Request  $request
     * @param Auditory $auditory
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Auditory $auditory) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:auditories,name,' . $auditory->getKey(),
        ], [
            'name.unique' => 'Данная аудитория уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only(['name', 'is_favorite']);
        $auditory->update($frd);

        if (!isset($frd['is_favorite'])) {
            $auditory->setIsFavorite(false);
        }
        $auditory->save();

        $message = [
            'type' => 'success',
            'text' => 'Аудитория успешно обновлена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Auditory $auditory
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Auditory $auditory) {
        $auditory->delete();

        $message = [
            'type' => 'success',
            'text' => 'Аудитория «' . $auditory->getName() . '» успешно удалена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Auditory $auditory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorite(Request $request, Auditory $auditory) {
        $frd = $request->only(['is_favorite']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['is_favorite']) && 'on' === $frd['is_favorite']) {
            $auditory->setIsFavorite(true);
            $auditory->save();
            $message['message']['text'] = 'Аудитория «' . $auditory->getName() . '» добавлена в избранное';
        } else {
            $auditory->setIsFavorite(false);
            $auditory->save();
            $message['message']['text'] = 'Аудитория «' . $auditory->getName() . '» удалена из избранного';
        }

        $response = response()->json($message);

        return $response;
    }
}
