<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 08.08.2019
 * Time: 10:54
 */

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Articles\ArticleLabel;
use App\Models\Organizations\Organization;
use App\Models\Products\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller {
    /**
     * @var Article
     */
    protected $articles;

    /**
     * @var Category
     */
    protected $categories;

    /**
     * ArticleController constructor.
     *
     * @param Article  $articles
     * @param Category $categories
     */
    public function __construct(Article $articles, Category $categories) {
        $this->articles = $articles;
        $this->categories = $categories;

        $this->middleware(['permission:articles--create'])->only(['create', 'store']);
        $this->middleware(['permission:articles--read'])->only(['index', 'show']);
        $this->middleware(['permission:articles--update'])->only(['edit', 'update']);
        $this->middleware(['permission:articles--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();

        $articles = $this->articles->filter($frd)
                                   ->ordering($frd)
                                   ->with('cover')
                                   ->paginate($frd['perPage'] ?? $this->articles->getPerPage());
        $categoriesList = Category::getAllCategoriesBlogList();

        return view('articles.index', compact('frd', 'articles', 'categoriesList'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        $categoriesList = $this->categories::getAllCategoriesBlogList();
        $organizationsList = Organization::getOrganizationsList();
        $labelsList = ArticleLabel::getArticleLabelsList();

        return view('articles.create', compact('categoriesList', 'organizationsList', 'labelsList'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), Article::getRules(), Article::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only([
            'images', //Только id главного изображения
            'textImages', //Массив с id изображений из текста
            'name',
            'short_description',
            'author',
            'is_actual',
            'text',
            'editor', //JSON текста статьи для возможности дальнейшего редактирования
            'categories',
            'organization_id',
            'article_label_id', //Лейбл статьи
        ]);
        $article = $this->articles->create();
        $article->updateArticle($frd);
        $article->creator()->associate(\Auth::user());
        $article->save();

        $message = [
            'type' => 'success',
            'text' => 'Статья успешно создана',
        ];

        return response()->json($message, 200);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Article $article) {
        $categories = $article->getCategoriesNames();
        $organizationsList = Organization::getOrganizationsList();
        $labelsList = ArticleLabel::getArticleLabelsList();

        return view('articles.show', compact('article', 'categories', 'organizationsList', 'labelsList'));
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Article $article) {
        $categoriesList = $this->categories::getAllCategoriesBlogList();
        $categoriesId = $article->getCategoriesId();
        $coverLink = $article->getCoverLink();
        $coverId = $article->getCoverId();
        $coverLinks = $article->getCoverLinks();
        $organizationsList = Organization::getOrganizationsList();
        $labelsList = ArticleLabel::getArticleLabelsList();

        return view('articles.edit', compact('article', 'categoriesId', 'categoriesList', 'coverLinks', 'coverLink', 'coverId', 'organizationsList', 'labelsList'));
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, Article $article) {
        $validator = Validator::make($request->all(), Article::getRules(), Article::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only([
            'images', //Только id главного изображения
            'textImages', //Массив с id изображений из текста
            'name',
            'short_description',
            'author',
            'is_actual',
            'text',
            'editor', //JSON текста статьи для возможности дальнейшего редактирования
            'categories',
            'organization_id',
            'article_label_id', //Лейбл статьи
        ]);

        $article->updateArticle($frd);

        $message = [
            'type' => 'success',
            'text' => 'Статья успешно обновлена',
        ];

        return response()->json($message, 200);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Article $article) {
        $article->delete();

        $message = [
            'type' => 'success',
            'text' => 'Статья успешно удалена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }
}
