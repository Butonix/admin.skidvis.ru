<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 08.08.2019
 * Time: 10:53
 */

namespace App\Http\Controllers\Api\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Resources\Articles\ArticleCollection;
use App\Http\Resources\Articles\Article as ArticleResource;
use App\Http\Resources\Reviews\ReviewCollection;
use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Articles\Article;
use App\Models\Products\Product;
use App\Models\Reviews\Review;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller {
    /**
     * @var Article
     */
    protected $articles;

    /**
     * ArticleController constructor.
     *
     * @param Article $articles
     */
    public function __construct(Article $articles, Review $reviews) {
        $this->articles = $articles;
		$this->reviews = $reviews;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $frd = $request->all();
        $responseTypeId = $frd['responseTypeId'] ?? Article::ARTICLES_RESPONSE_TYPE_SIMPLE_ACTUAL;
        $responseTypeId = (int)$responseTypeId;
        $response = [];

        if ($responseTypeId === Article::ARTICLES_RESPONSE_TYPE_SIMPLE_ACTUAL) {
            //Получение статей по-отдельности (отдельно актуальные, отдельно свежие)

            $articlesSimple = $this->articles->articlesSimple()
                                             ->filter($frd)
                                             ->ordering($frd)
                                             ->with('cover')
                                             ->paginate($frd['perPage'] ?? $this->articles->getPerPageSimpleArticles());
            $articlesActual = $this->articles->articlesActual(true)
                                             ->filter($frd)
                                             ->ordering($frd)
                                             ->with('cover')
                                             ->paginate($this->articles->getPerPageActualArticles());
            $response = [
                'articles' => [
                    'simple' => [
                        'list' => (new ArticleCollection($articlesSimple))->additional([
                            'meta' => [
                                'type'         => Article::ARTICLES_TYPE_LIST,
                                'responseType' => $responseTypeId,
                            ],
                        ]),
                    ],
                    'actual' => [
                        'list' => (new ArticleCollection($articlesActual))->additional([
                            'meta' => [
                                'type'         => Article::ARTICLES_TYPE_LIST,
                                'responseType' => $responseTypeId,
                            ],
                        ]),
                    ],
                ],
            ];
        } elseif ($responseTypeId === Article::ARTICLES_RESPONSE_TYPE_ALL) {
            //Получение всех статей

            $articlesSimple = $this->articles->filter($frd)
                                             ->ordering($frd)
                                             ->with('cover')
                                             ->paginate($frd['perPage'] ?? $this->articles->getPerPage());
            $response = [
                'list' => (new ArticleCollection($articlesSimple))->additional([
                    'meta' => [
                        'type'         => Article::ARTICLES_TYPE_LIST,
                        'responseType' => $responseTypeId,
                    ],
                ]),
            ];
        } elseif ($responseTypeId === Article::ARTICLES_RESPONSE_TYPE_BOOKMARKS) {
            //Получение всех статей с доп.полями

            $articles = $this->articles->filter($frd)
                                             ->ordering($frd)
                                             ->with('cover')
                                             ->paginate($frd['perPage'] ?? $this->articles->getPerPage());
            $response = [
                'list' => (new ArticleCollection($articles))->additional([
                    'meta' => [
                        'type'         => Article::ARTICLES_TYPE_LIST,
                        'responseType' => $responseTypeId,
                    ],
                ]),
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Article $article) {

		$frd = $request->all();

        $article->incrementViews();
        $article->save();

		$reviews = $article->reviews()
			->filter($frd)
			->ordering($frd)
			->paginate($frd['perPage'] ?? $this->reviews->getPerPage());

        return response()->json([
            'article' => (new ArticleResource($article))->additional([
                'meta' => [
                    'type' => Article::ARTICLES_TYPE_DEFAULT,
                ],
            ]),
			'reviews' => new ReviewCollection($reviews),
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request) {
        return response()->json([
            'status' => Article::hasLatestArticles()
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function image(Request $request) {
        $validator = Validator::make($request->only(['image']), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $mainImages = Article::saveImage($frd['image'], Article::class);

        return response()->json([
            'image'  => $mainImages,
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Изображение успешно сохранено.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function textImages(Request $request) {
        $validator = Validator::make($request->only(['image']), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $mainImages = Article::saveImage($frd['image'], Article::class, 'text');

        //Дублирование параметра необходимо для корректной работы текстового редактора,
        //который загружает изображения с помощью данного метода.
        $mainImages['url'] = $mainImages['src'];

        return response()->json([
            'success' => 1,
            'file'    => $mainImages,
            'alert'   => [
                'type' => 'success',
                'text' => 'Изображение успешно сохранено.',
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMarkedArticle(Request $request, Article $article) {
        /**
         * @var User $user
         * @var Bookmark $bookmarkArticle
         */
        $user = \Auth::guard('api')->user();
        $bookmarkArticle = $article->bookmarks()->where('user_id', $user->getKey());

        if ($bookmarkArticle->exists()) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'warning',
                    'text' => 'Данная статья уже добавлена в избранное.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $bookmarkArticle = $article->bookmarks()->save(new Bookmark());
        $bookmarkArticle->user()->associate($user);
        $bookmarkArticle->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Статья успешно добавлена в избранное.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteMarkedArticle(Request $request, Article $article) {
        /**
         * @var User $user
         * @var Bookmark $bookmarkArticle
         */
        $user = \Auth::guard('api')->user();
        $bookmarkArticle = $article->bookmarks()->where('user_id', $user->getKey());

        if (!$bookmarkArticle) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Невозможно удалить, статья не отмечена как избранная.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $bookmarkArticle->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Статья успешно удалена из избранного.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

	/**
	 * @param Request $request
	 * @param Product $product
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function reviews(Request $request, Article $article) {
		$frd = $request->all();
		$reviews = $article->reviews()
			->filter($frd)
			->ordering($frd)
			->paginate($frd['perPage'] ?? (new Review())->getPerPage());

		return response()->json([
			'list' => new ReviewCollection($reviews),
		], 200);
	}
}
