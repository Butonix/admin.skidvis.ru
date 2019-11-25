<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Products;


use App\Http\Controllers\Controller;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller {
    /**
     * @var Tag
     */
    protected $tags;

    /**
     * TagController constructor.
     *
     * @param Tag $tags
     */
    public function __construct(Tag $tags) {
        $this->tags = $tags;

        $this->middleware(['permission:tags--create'])->only(['create', 'store']);
        $this->middleware(['permission:tags--read'])->only(['index', 'show']);
        $this->middleware(['permission:tags--update'])->only(['edit', 'update']);
        $this->middleware(['permission:tags--delete'])->only(['destroy', 'actionsDestroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Теги');
        $frd = $request->all();
        $tags = $this->tags->filter($frd)
                           ->orderBy('name', 'ASC')
                           ->paginate($frd['perPage'] ?? $this->tags->getPerPageForAdminPanel());

        return view('products.tags.index', compact('frd', 'tags'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание тега');
        $frd = $request->all();
        $tags = $this->tags::orderByDesc('id')->take(20)->get();

        return view('products.tags.create', compact('tags'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:tags',
        ], [
            'name.unique' => 'Данный тег уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $tag = $this->tags->create($frd);
        $frdSearch = [];

        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }

        $frd = $frdSearch;
        $tags = $this->tags->orderByDesc('id')->get();
        $html = view('products.tags.components._lastCreatedTags', compact('frd', 'tags'))->render();
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Тег успешно добавлен.',
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
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Tag $tag) {
        SEOMeta::setTitle($tag->getName());
        return view('products.tags.show', compact('tag'));
    }

    /**
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Tag $tag) {
        SEOMeta::setTitle($tag->getName() . ' - редактирование');
        return view('products.tags.edit', compact('tag'));
    }

    /**
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tag $tag) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:tags,name,' . $tag->getKey(),
        ], [
            'name.unique' => 'Данный тег уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only(['name']);
        $tag->update($frd);

        $message = [
            'type' => 'success',
            'text' => 'Тег успешно обновлен',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Tag $tag) {
        $tag->delete();

        $message = [
            'type' => 'success',
            'text' => 'Тег «' . $tag->getName() . '» успешно удален',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->only([
            'tags',
        ]);
        $this->tags->destroy($frd['tags']);
        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $tags = $this->tags->filter($frd)
                           ->orderBy('name', 'ASC')
                           ->paginate($frd['perPage'] ?? $this->tags->getPerPage());
        $html = view('products.tags.components._index', compact('frd', 'tags'))->render();
        $flashMessage = [
            'type'    => 'success',
            'text'    => 'Теги успешно удалены.',
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html,
            ],
        ];
        $response = response()->json($flashMessage);

        return $response;
    }
}
