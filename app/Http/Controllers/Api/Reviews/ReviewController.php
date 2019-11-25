<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 25.07.2019
 * Time: 12:59
 */

namespace App\Http\Controllers\Api\Reviews;


use App\Http\Controllers\Controller;
use App\Http\Resources\Reviews\ReviewCollection;
use App\Models\Articles\Article;
use App\Models\Organizations\Organization;
use App\Models\Products\Product;
use App\Models\Reviews\Like;
use App\Models\Reviews\Rating;
use App\Models\Reviews\Review;
use App\Http\Resources\Reviews\Review as ReviewResource;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller {
    /**
     * @var Review
     */
    protected $reviews;

    /**
     * ReviewController constructor.
     *
     * @param Review $reviews
     */
    public function __construct(Review $reviews) {
        $this->reviews = $reviews;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $frd = $request->all();
        $reviews = $this->reviews->filter($frd)
                                 ->ordering($frd)
                                 ->paginate($frd['perPage'] ?? $this->reviews->getPerPage());

        return response()->json([
            'list' => new ReviewCollection($reviews),
        ], 200);
    }

    /**
     * @param Request $request
     * @param Review  $review
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeReview(Request $request, Review $review) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();
        $leftUserLike = $review->likes()->where('user_id', $user->getKey())->exists();

        if ($leftUserLike) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Вы уже оценивали данный отзыв.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $review->likes_count++;
        $review->save();
        /**
         * @var Like $like
         */
        $like = $review->likes()->save(new Like());
        $like->user()->associate($user);
        $like->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Вы успешно оценили отзыв.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @param Review  $review
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlikeReview(Request $request, Review $review) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();
        $like = $review->likes()->where('user_id', $user->getKey());
        $leftUserLike = $like->exists();

        if (!$leftUserLike) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Отзыв ранее не был оценен.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $review->likes_count--;
        $review->save();
        $like->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Вы успешно отменили свою оценку отзыву.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeForOrganization(Request $request, Organization $organization) {
        $validator = Validator::make($request->all(), Review::getRulesForOrganizations(), Review::getMessagesForOrganizations());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /**
         * @var User   $user
         * @var Review $review
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->only(['text', 'rating']);
        $review = $organization->reviews()->save(new Review($frd));
        $review->user()->associate($user);
        $review->save();
        $ratingForOrganization = $organization->ratings()->where('user_id', $user->getKey());
        $rating = $ratingForOrganization->first();

        if ($ratingForOrganization->doesntExist()) {
            $rating = new Rating();
            $rating->user()->associate($user);
            $organization->ratings()->save($rating);
        }

        $rating->setRating($frd['rating']);
        $rating->save();
        $organization->calculateRating();
        $organization->save();

        return response()->json([
            'review' => new ReviewResource($review),
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Отзыв успешно добавлен.',
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
    public function storeForArticle(Request $request, Article $article) {
        $validator = Validator::make($request->all(), Review::getRulesForArticles(), Review::getMessagesForArticles());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /**
         * @var User $user
         * @var Review $review
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->only(['text']);
        $review = $article->reviews()->save(new Review($frd));
        $review->user()->associate($user);
        $review->save();

        return response()->json([
            'review' => new ReviewResource($review),
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Отзыв успешно добавлен.',
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
    public function storeForProduct(Request $request, Product $product) {
        $validator = Validator::make($request->all(), Review::getRulesForProducts(), Review::getMessagesForProducts());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /**
         * @var User $user
         * @var Review $review
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->only(['text', 'pros', 'cons']);
        $review = $product->reviews()->save(new Review($frd));
        $review->user()->associate($user);
        $review->save();

        return response()->json([
            'review' => new ReviewResource($review),
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Отзыв успешно добавлен.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @param Review  $review
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Review $review) {
        $review->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Отзыв успешно удалён.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }
}
