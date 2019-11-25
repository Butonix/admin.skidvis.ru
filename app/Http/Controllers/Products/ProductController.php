<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 15:57
 */

namespace App\Http\Controllers\Products;


use App\Http\Controllers\Controller;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * @var Category
     */
    protected $categories;

    /**
     * ProductController constructor.
     *
     * @param Product $products
     */
    public function __construct(Product $products, Tag $tags, Category $categories) {
        $this->products = $products;
        $this->tags = $tags;
        $this->categories = $categories;

        $this->middleware(['permission:products--create'])->only(['create', 'store']);
        $this->middleware(['permission:products--read'])->only(['index', 'show', 'allProducts', 'productsShow']);
        $this->middleware(['permission:products--update'])->only(['edit', 'update']);
        $this->middleware(['permission:products--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts(Request $request) {
        $frd = $request->all();
        $user = \Auth::user();

        if ($user->isSuperAdministrator()) {
            $products = $this->products->filter($frd)
                                       ->ordering($frd)
                                       ->with(['sliderImages', 'organization'])
                                       ->paginate($frd['perPage'] ?? $this->products->getPerPage());
        } elseif ($user->isAdministrator() || $user->isManager()) {
            $products = $this->products::productsByUser($user->getKey())
                                       ->filter($frd)
                                       ->ordering($frd)
                                       ->with(['sliderImages', 'organization'])
                                       ->paginate($frd['perPage'] ?? $this->products->getPerPage());
        } else {
            $products = [];
        }

        $categoriesList = $this->categories::getAllCategoriesList();
        $tagsList = $this->tags::getAllTagsList();

        return view('products.all', compact('frd', 'products', 'tagsList', 'categoriesList'));
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsShow(Request $request, Product $product) {
        $chosenTags = $product->getTags()->keyBy('id')->keys();
        $tagsList = $this->tags::getAllTagsList();
        $chosenPoints = $product->getPoints();
        SEOMeta::setTitle($product->getName());
        $organization = $product->getOrganization();

        //dd($product->countImages());

        return view('products.show', compact('organization', 'product', 'tagsList', 'chosenTags', 'chosenPoints'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Organization $organization) {
        SEOMeta::setTitle('Акции');
        $frd = $request->all();
        $products = $organization->products()
                                 ->filter($frd)
                                 ->with(['sliderImages', 'organization'])
                                 ->paginate($frd['perPage'] ?? $this->products->getPerPage());
        $tagsList = $this->tags::getAllTagsList();
        $categoriesList = $this->categories::getAllCategoriesList();

        return view('products.index', compact('frd', 'organization', 'products', 'tagsList', 'categoriesList'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Organization $organization) {
        SEOMeta::setTitle('Создание акции');
        $tagsList = $this->tags::getAllTagsList();
        $categoriesList = $this->categories::getAllCategoriesList();
        $pointsList = $organization->getPointsList();
        $coversLinks = [];

        return view('products.create', compact('organization', 'tagsList', 'pointsList', 'categoriesList', 'coversLinks'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(Request $request, Organization $organization) {
        /**
         * @var Product $product
         */
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string:max255',
            'description'  => 'required|string',
            'conditions'   => 'required|string',
            'category'     => 'required',
            'start_at'     => 'required|date',
            'end_at'       => 'nullable|date|after:start_at',
            'origin_price' => 'required|integer',
            'value'        => 'required|integer|max:100',
        ], [
            'end_at.after' => 'Дата ПО должна быть после даты С',
            'value.max'    => 'Скидка не может быть больше 100%',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $product = $organization->products()->save(new Product($frd));
        $product->points()->attach($frd['points']);
        $product->tags()->attach($frd['tags']);
        $product->categories()->attach($frd['category']);

        foreach ($frd['images'] as $key => $imageFile) {
            if (is_null($imageFile)) {
                continue;
            }

            $image = new Image();
            $image->download($imageFile);
            $product->images()->save($image);
        }

        $product->save();
        $message = [
            'type' => 'success',
            'text' => 'Акция успешно создана',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Organization $organization, Product $product) {
        $user = \Auth::user();
        $products = $organization->products();

        //Проверка если пользователь администратор\менеджер и организация, которую он открывает, ему не принадлежит,
        //то у него недостаточно прав для просмотра
        if (($user->isAdministrator() || $user->isManager()) && ($products->whereKey($product->getKey())
                                                                          ->doesntExist())) {
            abort(403);
        }

        $chosenTags = $product->getTags()->keyBy('id')->keys();
        $tagsList = $this->tags::getAllTagsList();
        $chosenPoints = $product->getPoints();
        SEOMeta::setTitle($product->getName());

        //dd($product->countImages());

        return view('products.show', compact('organization', 'product', 'tagsList', 'chosenTags', 'chosenPoints'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Organization $organization, Product $product) {
        $user = \Auth::user();
        $products = $organization->products();

        //Проверка если пользователь администратор\менеджер и организация, которую он открывает, ему не принадлежит,
        //то у него недостаточно прав для просмотра
        if (($user->isAdministrator() || $user->isManager()) && ($products->whereKey($product->getKey())
                                                                          ->doesntExist())) {
            abort(403);
        }

        SEOMeta::setTitle($product->getName() . ' - редактирование');
        $chosenTags = $product->getTags()->keyBy('id')->keys();
        $tagsList = $this->tags::getAllTagsList();
        $chosenCategories = $product->getCategories()->keyBy('id')->keys();
        $categoriesList = $this->categories::getAllCategoriesList();
        $chosenPoints = $product->getPoints()->keyBy('id')->keys();
        $pointsList = $organization->getPointsList();
        $coversLinks = $product->getImagesLinks();
        $coversCount = $product->countImages();

        return view('products.edit', compact('organization', 'product', 'tagsList', 'chosenTags', 'pointsList', 'chosenPoints', 'categoriesList', 'chosenCategories', 'coversLinks', 'coversCount'));
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Product      $product
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function update(Request $request, Organization $organization, Product $product) {
        /**
         * @var Product $product
         */
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string:max255',
            'description'  => 'required|string',
            'conditions'   => 'required|string',
            'start_at'     => 'required|date',
            'end_at'       => 'nullable|date|after:start_at',
            'origin_price' => 'required|integer',
            'value'        => 'required|integer|max:100',
            'category'     => 'required',
        ], [
            'end_at.after' => 'Дата ПО должна быть после даты С',
            'value.max'    => 'Скидка не может быть больше 100%',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $product->update($frd);

        $chosenTags = $product->getTags()->keyBy('id')->keys()->toArray();
        $addTags = array_diff($frd['tags'], $chosenTags);
        $removeTags = array_diff($chosenTags, $frd['tags']);
        $product->tags()->detach($removeTags);
        $product->tags()->attach($addTags);

        $chosenPoints = $product->getPoints()->keyBy('id')->keys()->toArray();
        $addPoints = array_diff($frd['points'], $chosenPoints);
        $removePoints = array_diff($chosenPoints, $frd['points']);
        $product->points()->detach($removePoints);
        $product->points()->attach($addPoints);

        $chosenCategory = $product->getCategories()->keyBy('id')->keys()->first();
        $frd['category'] = (int)$frd['category'];
        if ($chosenCategory !== $frd['category']) {
            $product->categories()->detach($chosenCategory);
        }
        $product->categories()->attach($frd['category']);

        foreach ($frd['images'] as $key => $imageFile) {
            if (is_null($imageFile)) {
                continue;
            }

            $oldImage = $product->getImage($key);
            if (!is_null($oldImage)) {
                $oldImage->forceDelete();
                $oldImage->delete();
            }

            $image = new Image();
            $image->download($imageFile);
            $product->images()->save($image);
        }

        $product->save();
        $message = [
            'type' => 'success',
            'text' => 'Акция успешно обновлена',
        ];

        return redirect()->back()->with('flash_message', $message);
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
        $product->delete();

        $message = [
            'type' => 'success',
            'text' => 'Акция успешно удалена',
        ];

        return redirect()->back()->with('flash_message', $message);
    }
}
