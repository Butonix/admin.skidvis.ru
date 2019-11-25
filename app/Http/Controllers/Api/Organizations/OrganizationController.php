<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 18:59
 */

namespace App\Http\Controllers\Api\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Resources\Organizations\OrganizationCollection;
use App\Http\Resources\Products\ProductCollection;
use App\Http\Resources\Reviews\ReviewCollection;
use App\Http\Resources\Users\UserCollection;
use App\Models\Files\Image;
use App\Http\Resources\Organizations\Organization as OrganizationResource;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Products\Product;
use App\Models\Reviews\Review;
use App\Models\Social\SocialAccount;
use App\Models\Social\SocialNetwork;
use App\Models\Users\User;
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
     * @var User
     */
    protected $users;

    /**
     * @var Review
     */
    protected $reviews;

    /**
     * @var Product
     */
    protected $products;

    /**
     * OrganizationController constructor.
     *
     * @param Organization  $organizations
     * @param SocialNetwork $socialNetworks
     * @param User          $users
     * @param Review        $reviews
     * @param Product       $products
     */
    public function __construct(Organization $organizations, SocialNetwork $socialNetworks, User $users, Review $reviews, Product $products) {
        $this->organizations = $organizations;
        $this->socialNetworks = $socialNetworks;
        $this->users = $users;
        $this->reviews = $reviews;
        $this->products = $products;

        $this->middleware(['permission:organizations--create'])->only(['store']);
        $this->middleware(['permission:organizations--read'])->only(['index', 'show']);
        $this->middleware(['permission:organizations--update'])->only(['edit', 'update', 'unpublish']);
        $this->middleware(['permission:organizations--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request) {
        $frd = $request->all();
        $organizations = $this->organizations->publicOrganizations()
                                             ->filter($frd)//->orderBy('name', 'ASC')
                                             ->ordering($frd)
                                             ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());

        $organizations = new OrganizationCollection($organizations);

        return response()->json([
            'list' => $organizations,
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function organizationsShow(Request $request, Organization $organization) {
        $frd = $request->all();
        $reviews = $organization->reviews()->ordering($frd)->paginate($reviewsPerPage ?? $this->reviews->getPerPage());

        $products = $organization->products()
                                 ->publicProducts()
                                 ->filter($frd)
                                 ->ordering($frd)
                                 ->paginate($productsPerPage ?? $this->products->getPerPageForPublicOrganizations());

        return response()->json([
            'organization' => (new OrganizationResource($organization))->additional([
                'meta' => [
                    'needShortDescription' => false,
                    'needCreatedAt'        => false,
                    'needCreator'          => false,
                    'needOperationMode'    => false,
                    'city_id'              => $frd['city_id'] ?? null,
                ],
            ]),
            'reviews'      => new ReviewCollection($reviews),
            'products'     => (new ProductCollection($products))->additional([
                'meta' => [
                    'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
                ],
            ]),
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        /**
         * Выдача организаций только для администраторов/менеджеров
         *
         * @var User $user
         */
        $frd = $request->all();
        $user = \Auth::guard('api')->user();

        if ($user->isSuperAdministrator()) {
            $organizations = $this->organizations->filter($frd)
                                                 ->ordering($frd)
                                                 ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());
        } else {
            $organizations = $this->organizations->organizationsByUser($user->getKey())
                                                 ->filter($frd)
                                                 ->ordering($frd)
                                                 ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());
        }

        $organizations = new OrganizationCollection($organizations);

        return response()->json([
            'list' => $organizations,
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), Organization::getRules(), Organization::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->all();
        $organization = $this->organizations->create($frd);
        $organization->updateOrganization($frd, $user);
        $organization->creator()->associate($user);
        $organization->save();

        return response()->json([
            'status'       => 'OK',
            'organization' => new OrganizationResource($organization),
            'alert'        => [
                'type' => 'success',
                'text' => 'Организация успешно создана.',
            ],
            'action'       => [
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
    public function show(Request $request, Organization $organization) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для просмотра данной организации',
                ],
            ], 403);
        }

        return response()->json([
            'organization' => new OrganizationResource($organization),
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, Organization $organization) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для редактирования данной организации',
                ],
            ], 403);
        }

        return response()->json([
            'organization' => new OrganizationResource($organization),
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, Organization $organization) {
        $validator = Validator::make($request->all(), Organization::getRules(), Organization::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->all();
        $organization->update($frd);
        $organization->updateOrganization($frd, $user);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Организация успешно обновлена.',
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
     * @throws \Exception
     */
    public function servicesUpdate(Request $request, Organization $organization) {

        /**
         * @var User $user
         */
        $organization->updateServices($request);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Организация успешно обновлена.',
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
     * @throws \Exception
     */
    public function destroy(Request $request, Organization $organization) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для удаления данной организации',
                ],
            ], 403);
        }

        $organization->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Организация успешно удалена',
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
    public function image(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $mainImages = Organization::saveImage($frd['image'], Organization::class);

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
    public function logo(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $image = Organization::saveLogo($frd['image']);

        return response()->json([
            'image'  => $image,
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Логотип успешно сохранен.',
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
    public function miniLogo(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $image = Organization::saveMiniLogo($frd['image']);

        return response()->json([
            'image'  => $image,
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Логотип успешно сохранен.',
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
    public function unpublish(Request $request, Organization $organization) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для снятия с публикации данной организации',
                ],
            ], 403);
        }

        if (!$organization->isPublished()) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Организация уже снята с публикации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $frd = $request->only(['is_published']);
        $organization->unpublishOrganization($frd['is_published'] ?? false);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Организация успешно снята с публикации.',
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
    public function reviews(Request $request, Organization $organization) {
        $frd = $request->all();
        $reviews = $organization->reviews()
                                ->filter($frd)
                                ->ordering($frd)
                                ->paginate($frd['perPage'] ?? (new Review())->getPerPage());

        return response()->json([
            'list' => new ReviewCollection($reviews),
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function products(Request $request, Organization $organization) {
        $frd = $request->all();
        $products = $organization->products()
                                 ->publicProducts()
                                 ->filter($frd)
                                 ->ordering($frd)
                                 ->paginate($frd['perPage'] ?? $this->products->getPerPage());
        $response['list'] = (new ProductCollection($products))->additional([
            'meta' => [
                'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE,
                'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
            ],
        ]);

        return response()->json($response, 200);
    }
}
