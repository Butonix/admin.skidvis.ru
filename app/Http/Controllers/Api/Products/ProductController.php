<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 15:57
 */

namespace App\Http\Controllers\Api\Products;


use App\Http\Controllers\Controller;
use App\Http\Resources\Organizations\PointCollection;
use App\Http\Resources\Products\ProductCollection;
use App\Http\Resources\Reviews\ReviewCollection;
use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Products\Auditory;
use App\Models\Products\Holiday;
use App\Models\Organizations\Organization;
use App\Models\Organizations\Point;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Http\Resources\Products\Product as ProductResource;
use App\Models\Products\Tag;
use App\Models\Reviews\Review;
use App\Models\Social\SocialNetwork;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ProductController extends Controller {
    /**
     * @var Product
     */
    protected $products;

    /**
     * @var Tag
     */
    protected $tags;

    /**
     * @var Auditory
     */
    protected $auditories;

    /**
     * @var Holiday
     */
    protected $holidays;

    /**
     * @var Category
     */
    protected $categories;

    /**
     * @var SocialNetwork
     */
    protected $socialNetworks;

    /**
     * @var Review
     */
    protected $reviews;

    /**
     * ProductController constructor.
     *
     * @param Product       $products
     * @param Tag           $tags
     * @param Category      $categories
     * @param SocialNetwork $socialNetworks
     * @param Review        $reviews
     * @param Auditory      $auditories
     * @param Holiday       $holidays
     */
    public function __construct(Product $products, Tag $tags, Category $categories, SocialNetwork $socialNetworks, Review $reviews, Auditory $auditories, Holiday $holidays) {
        $this->products = $products;
        $this->tags = $tags;
        $this->auditories = $auditories;
        $this->holidays = $holidays;
        $this->categories = $categories;
        $this->socialNetworks = $socialNetworks;
        $this->reviews = $reviews;

        $this->middleware(['permission:products--create'])->only(['store']);
        $this->middleware(['permission:products--read'])->only(['index', 'show']);
        $this->middleware(['permission:products--update'])->only(['edit', 'update', 'unpublish']);
        $this->middleware(['permission:products--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts(Request $request) {
        $frd = $request->all();

        if (isset($frd['with_advertisement'])) {

			$advertisements = $this->products->publicProducts()
				->filter($frd)
				->productsIsAdvertisement()
				->inRandomOrder()
				->productsWith()
				->take(6)->get();

			$advertisementsIds = $advertisements->pluck('id')->toArray();

			$advertisementsCount = $advertisements->count();

			$products = $this->products->publicProducts()
				->filter($frd)
				->productsWhereNotIn($advertisementsIds)
				->ordering($frd)
				->productsWith()
				->paginate(($frd['perPage'] ?? $this->products->getPerPageForPublic()) - $advertisementsCount);

			$advertisements = $advertisements->concat($products->items());

			$products           = new LengthAwarePaginator($advertisements, ($products->total() + $advertisementsCount), ($frd['perPage'] ?? $this->products->getPerPageForPublic()), $products->currentPage(), [
				'path' => request()->url(),
			]);


			$additional = [
				'meta' => [
					'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE,
					'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
					'pointsFilter'  => [
						'city_id' => $frd['city_id'] ?? null,
					],
				],
			];

			$response['list'] = (new ProductCollection($products))->additional($additional);

		} else {
			$products = $this->products->publicProducts()
				->filter($frd)
				->ordering($frd)
				->productsWith()
				->paginate($frd['perPage'] ?? $this->products->getPerPageForPublic());

			$response['list'] = (new ProductCollection($products))->additional([
				'meta' => [
					'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE,
					'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
					'pointsFilter'  => [
						'city_id' => $frd['city_id'] ?? null,
					],
				],
			]);
		}



        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsShowMap(Request $request, Product $product) {
        $frd = $request->all();

        $points = Point::pointsByProduct($product->getKey())->paginate($frd['perPage'] ?? (new Point)->getPerPageForMap());

        $response['list'] = (new PointCollection($points))->additional([
            'meta' => [
                'needProducts' => true,
                'filter'       => $frd,
                'type'         => Point::POINTS_TYPE_FOR_MAP,
                'scheduleType' => Point::POINT_SCHEDULE_TYPE_PUBLIC,
            ],
        ]);

        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsShow(Request $request, Product $product) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        //Если акция не опубликована и пользователь гость/не администратор/не менеджер, то доступ отклоняется
        if ($product->isUnpublished() && (\Auth::guard('api')
                                               ->guest() || (optional($user)->isNotAdministrator() && optional($user)->isNotManager()))) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Акция не найдена.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }

        $frd = $request->all();
        $product->incrementViews();
        $product->save();
        $reviews = $product->reviews()
                           ->filter($frd)
                           ->ordering($frd)
                           ->paginate($frd['perPage'] ?? $this->reviews->getPerPage());
        $response = [
            'product' => (new ProductResource($product))->additional([
                'meta' => [
                    'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCT_SHOW,
                    'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
                    'pointsFilter'  => [
                        'city_id' => $frd['city_id'] ?? null,
                    ],
                ],
            ]),
            'reviews' => new ReviewCollection($reviews),
        ];

        return response()->json($response, 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Organization $organization) {
        /**
         * Метод для просмотра акций конкретной организации, только для администраторов/менеджеров
         *
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для просмотра акций',
                ],
            ], 403);
        }

        $frd = $request->all();
        $products = $organization->products()
                                 ->filter($frd)
                                 ->ordering($frd)
                                 ->productsWith()
                                 ->paginate($frd['perPage'] ?? $this->products->getPerPage());
        $response['list'] = (new ProductCollection($products))->additional([
            'meta' => [
                'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE,
                'typeOfPublish' => Product::PRODUCT_PUBLISH_INDIVIDUAL,
            ],
        ]);

        return response()->json($response, 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request, Organization $organization) {
        /**
         * @var Product $product
         */
		$frd = $request->all();

		if(isset($frd['is_perpetual']) && $frd['is_perpetual'] && empty($frd['start_at']) && empty($frd['end_at'])){
			$frd['start_at'] = Carbon::now();
			$frd['end_at'] = Carbon::now()->addDay();
		}

        $validator = Validator::make($frd, Product::getRules(), Product::getMessages());


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $emptyFields = [];
        $needCheckValue = true;

        if (isset($frd['currency_id'])) {
            if ($frd['currency_id'] === 3) {
                $needCheckValue = false;
            }
        } else {
            $emptyFields[] = 'валюта';
        }

        if ($needCheckValue && !isset($frd['value'])) {
            $emptyFields[] = 'величина скидки';
        }

        if (!empty($emptyFields)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type'  => 'error',
                    'title' => 'Заполните поля:',
                    'text'  => implode(', ', $emptyFields),
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 422);
        }

        if ($frd['currency_id'] === 1) {
            if ($frd['value'] > 100) {
                return response()->json([
                    'status' => 'error',
                    'alert'  => [
                        'type' => 'error',
                        'text' => 'Скидка не может быть больше 100%.',
                    ],
                    'action' => [
                        'type' => null,
                        'url'  => null,
                    ],
                ], 422);
            }
        } elseif ($frd['currency_id'] === 2) {
            if (isset($frd['origin_price'])) {
                if ($frd['value'] > $frd['origin_price']) {
                    return response()->json([
                        'status' => 'error',
                        'alert'  => [
                            'type' => 'error',
                            'text' => 'Скидка не может быть больше изначальной величины.',
                        ],
                        'action' => [
                            'type' => null,
                            'url'  => null,
                        ],
                    ], 422);
                }
            }
        }

        $product = $organization->products()->save(new Product());
        $product->updateProduct($frd);
        $product->creator()->associate(\Auth::guard('api')->user());
        $product->save();

        return response()->json([
            'status'  => 'OK',
            'product' => (new ProductResource($product))->additional([
                'meta' => [
                    'typeOfPublish' => Product::PRODUCT_PUBLISH_INDIVIDUAL,
                ],
            ]),
            'alert'   => [
                'type' => 'success',
                'text' => 'Акция успешно создана.',
            ],
            'action'  => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Organization $organization, Product $product) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для просмотра акции',
                ],
            ], 403);
        }

        if ($organization->products()->whereKey($product)->exists()) {
            return response()->json([
                'product' => (new ProductResource($product))->additional([
                    'meta' => [
                        'typeOfPoints'  => Product::POINTS_FOR_PUBLIC_PRODUCT_SHOW,
                        'typeOfPublish' => Product::PRODUCT_PUBLISH_INDIVIDUAL,
                    ],
                ]),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данная акция не найдена в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Organization $organization, Product $product) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для редактирования акции',
                ],
            ], 403);
        }

        if ($organization->products()->whereKey($product)->exists()) {
            return response()->json([
                'product' => (new ProductResource($product))->additional([
                    'meta' => [
                        'typeOfPoints'  => Product::POINTS_IDS_TYPE,
                        'typeOfPublish' => Product::PRODUCT_PUBLISH_INDIVIDUAL,
                    ],
                ]),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данная акция не найдена в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, Organization $organization, Product $product) {
        /**
         * @var Product $product
         */

		$frd = $request->all();

		if(isset($frd['is_perpetual']) && $frd['is_perpetual'] && empty($frd['start_at']) && empty($frd['end_at'])){
			$frd['start_at'] = Carbon::now();
			$frd['end_at'] = Carbon::now()->addDay();
		}

        $validator = Validator::make($frd, Product::getRules(), Product::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $emptyFields = [];
        $needCheckValue = true;

        if (isset($frd['currency_id'])) {
            if ($frd['currency_id'] === 3) {
                $needCheckValue = false;
            }
        } else {
            $emptyFields[] = 'валюта';
        }

        if ($needCheckValue && !isset($frd['value'])) {
            $emptyFields[] = 'величина скидки';
        }

        if (!empty($emptyFields)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type'  => 'error',
                    'title' => 'Заполните поля:',
                    'text'  => implode(', ', $emptyFields),
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 422);
        }

        if ($frd['currency_id'] === 1) {
            if ($frd['value'] > 100) {
                return response()->json([
                    'status' => 'error',
                    'alert'  => [
                        'type' => 'error',
                        'text' => 'Скидка не может быть больше 100%.',
                    ],
                    'action' => [
                        'type' => null,
                        'url'  => null,
                    ],
                ], 422);
            }
        } elseif ($frd['currency_id'] === 2) {
            if (isset($frd['origin_price'])) {
                if ($frd['value'] > $frd['origin_price']) {
                    return response()->json([
                        'status' => 'error',
                        'alert'  => [
                            'type' => 'error',
                            'text' => 'Скидка не может быть больше изначальной величины.',
                        ],
                        'action' => [
                            'type' => null,
                            'url'  => null,
                        ],
                    ], 422);
                }
            }
        }

        $product->updateProduct($frd);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Акция успешно обновлена.',
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
     * @param Product      $product
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Organization $organization, Product $product) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для удаления акции',
                ],
            ], 403);
        }

        if (!$organization->products()->whereKey($product)->exists()) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данная акция не найдена в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Акция успешно удалена.',
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
        $mainImages = Organization::saveImage($frd['image'], Product::class);

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
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMarkedProduct(Request $request, Product $product) {
        /**
         * @var User     $user
         * @var Bookmark $bookmarkProduct
         */
        $user = \Auth::guard('api')->user();
        $bookmarkProduct = $product->bookmarks()->where('user_id', $user->getKey());

        if ($bookmarkProduct->exists()) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'warning',
                    'text' => 'Данная акция уже добавлена в избранное.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $bookmarkProduct = $product->bookmarks()->save(new Bookmark());
        $bookmarkProduct->user()->associate($user);
        $bookmarkProduct->save();

        $organization = $product->getOrganization();
        $organization->wishlist_count++;
        $organization->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Акция успешно добавлена в избранное.',
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
     * @throws \Exception
     */
    public function deleteMarkedProduct(Request $request, Product $product) {
        /**
         * @var User     $user
         * @var Bookmark $bookmarkProduct
         */
        $user = \Auth::guard('api')->user();
        $bookmarkProduct = $product->bookmarks()->where('user_id', $user->getKey());

        if (!$bookmarkProduct) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Невозможно удалить, акция не отмечена как избранная.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $bookmarkProduct->delete();

        $organization = $product->getOrganization();
        $organization->wishlist_count--;
        $organization->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Акция успешно удалена из избранного.',
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
     * @param Review  $review
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroyReview(Request $request, Product $product, Review $review) {
        if (!$product->reviews()->whereKey($review)->exists()) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данный отзыв не найден в указанной акции.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }

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

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublish(Request $request, Product $product) {
        if (!$product->getIsPublished()) {
            return response()->json([
                'status' => 'OK',
                'alert'  => [
                    'type' => 'warning',
                    'text' => 'Акция уже снята с публикации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 200);
        }

        $frd = $request->only(['is_published']);

        if ($frd['is_published']) {
            $frd['is_published'] = false;
        }

        $product->setPublished($frd['is_published']);
        $product->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Акция успешно снята с публикации.',
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
    public function pointsDifferentTime(Request $request, Product $product) {
        $pointsOther = $product->getPointsWithDifferentTime();

        return response()->json([
            'list' => (new PointCollection($pointsOther))->additional([
                'meta' => [
                    'needProducts' => false,
                    'type'         => Point::POINTS_TYPE_DEFAULT,
                    'scheduleType' => Point::POINT_SCHEDULE_TYPE_PUBLIC,
                ],
            ]),
        ], 200);
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviews(Request $request, Product $product) {
        $frd = $request->all();
        $reviews = $product->reviews()
                           ->filter($frd)
                           ->ordering($frd)
                           ->paginate($frd['perPage'] ?? (new Review())->getPerPage());

        return response()->json([
            'list' => new ReviewCollection($reviews),
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function map(Request $request) {
        $frd = $request->all();

        $products = $this->products->productsByCoordinates([
            'latitudeMin'  => $frd['latitudeMin'],
            'longitudeMin' => $frd['longitudeMin'],
            'latitudeMax'  => $frd['latitudeMax'],
            'longitudeMax' => $frd['longitudeMax'],
        ])->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $this->products->getPerPageForMap());


        $response['list'] = (new ProductCollection($products))->additional([
            'meta' => [
                'type'          => Product::PRODUCTS_TYPE_DEFAULT,
                'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
            ],
        ]);

        return response()->json($response, 200);
    }
}
