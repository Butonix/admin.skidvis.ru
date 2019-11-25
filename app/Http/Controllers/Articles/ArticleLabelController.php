<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 11.08.2019
 * Time: 14:07
 */

namespace App\Http\Controllers\Articles;


use App\Http\Controllers\Controller;
use App\Models\Articles\ArticleLabel;
use App\Models\Files\Image;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleLabelController extends Controller {

    /**
     * @var ArticleLabel
     */
    protected $articleLabels;

    /**
     * ArticleLabelController constructor.
     *
     * @param ArticleLabel $articleLabels
     */
    public function __construct(ArticleLabel $articleLabels) {
        $this->articleLabels = $articleLabels;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Лэйблы для статей');
        $frd = $request->all();
        $labels = $this->articleLabels->filter($frd)
                                      ->orderBy('name', 'ASC')
                                      ->paginate($frd['perPage'] ?? $this->articleLabels->getPerPage());

        return view('articles.labels.index', compact('frd', 'labels'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание лейбла');
        $frd = $request->all();
        $labels = $this->articleLabels::orderByDesc('id')->take(20)->get();

        return view('articles.labels.create', compact('frd', 'labels'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:article_labels',
        ], [
            'name.unique' => 'Данный лейбл уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $label = $this->articleLabels->create($frd);
        $label->updateImage($frd['icon'] ?? null);
        $label->save();

        $message = [
            'type' => 'success',
            'text' => 'Категория успешно обновлена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param ArticleLabel $label
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, ArticleLabel $label) {
        SEOMeta::setTitle($label->getName());
        return view('articles.labels.show', compact('label'));
    }


    /**
     * @param Request      $request
     * @param ArticleLabel $label
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, ArticleLabel $label) {
        SEOMeta::setTitle($label->getName() . ' - редактирование');
        return view('articles.labels.edit', compact('label'));
    }

    /**
     * @param Request      $request
     * @param ArticleLabel $label
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, ArticleLabel $label) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:article_labels,name,' . $label->getKey(),
        ], [
            'name.unique' => 'Данный лейбл уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $label->update($frd);
        $label->updateImage($frd['icon'] ?? null);
        $label->save();

        $message = [
            'type' => 'success',
            'text' => 'Лейбл успешно обновлен',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param ArticleLabel $label
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, ArticleLabel $label) {
        $label->delete();

        $message = [
            'type' => 'success',
            'text' => 'Лейбл «' . $label->getName() . '» успешно удален',
        ];

        return redirect()->back()->with('flash_message', $message);
    }
}
