<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 15:54
 */

namespace App\Http\Controllers\Api\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Bookmarks\Bookmark;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class BookmarkController
 * @package App\Http\Controllers\Api\Bookmarks
 */
class BookmarkController extends Controller {
    /**
     * @var Bookmark
     */
    protected $bookmarks;

    /**
     * @var Product
     */
    protected $products;

    /**
     * @var Article
     */
    protected $articles;

    /**
     * BookmarkController constructor.
     *
     * @param Bookmark $bookmarks
     */
    public function __construct(Bookmark $bookmarks, Product $products, Article $articles) {
        $this->bookmarks = $bookmarks;
        $this->products = $products;
        $this->articles = $articles;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        /**
         * @var User $user
         */
        $frd = $request->all();
        $user = \Auth::guard('api')->user();

        $response = $user->getBookmarksArticlesItems($frd);

        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        /**
         * @var User $user
         */
        $frd = $request->all();
        $user = \Auth::guard('api')->user();
        Bookmark::saveArticles($frd['articles'] ?? [], $user);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Избранное успешно связано с аккаунтом',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }
}
