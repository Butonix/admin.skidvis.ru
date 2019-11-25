<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 27.06.2019
 * Time: 22:58
 */

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Files\Image;
use App\Models\Products\Category;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller {
    /**
     * @var Category
     */
    protected $categories;

    /**
     * CategoryController constructor.
     *
     * @param Category $categories
     */
    public function __construct(Category $categories) {
        $this->categories = $categories;

        $this->middleware(['permission:categories--create'])->only(['create', 'store']);
        $this->middleware(['permission:categories--read'])->only(['index', 'show']);
        $this->middleware(['permission:categories--update'])->only(['edit', 'update', 'favorite', 'forBlog', 'forProducts']);
        $this->middleware(['permission:categories--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();

        if (isset($frd['type'])) {
            $type = $frd['type'];

            if ($type === 'blog') {
                $frd['blog'] = true;
                $frd['products'] = $frd['favorites'] = null;
            } elseif ($type === 'products') {
                $frd['products'] = true;
                $frd['blog'] = $frd['favorites'] = null;
            } elseif ($type === 'favorites') {
                $frd['favorites'] = true;
                $frd['blog'] = $frd['products'] = null;
            }
        } else {
            $frd['blog'] = $frd['products'] = $frd['favorites'] = null;
        }

        SEOMeta::setTitle('Категории');
        $categories = $this->categories->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $this->categories->getPerPageForAdminPanel());

        return view('products.categories.index', compact('frd', 'categories'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание категории');
        $frd = $request->all();
        $categories = $this->categories::orderByDesc('id')->take(20)->get();

        return view('products.categories.create', compact('frd', 'categories'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:categories'
        ], [
            'name.unique' => 'Данная катеория уже существует'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only(['name','color', 'is_favorite', 'for_products', 'for_blog', 'icon']);
        $category = $this->categories->create($frd);
        $category->updateCategory($frd);

        //if (isset($frd['icon']) && !empty($frd['icon'])) {
        //    $icons = $frd['icon'];
        //
        //    foreach ($icons as $key => $newIconId) {
        //        if (isset($newIconId)) {
        //            $oldIconId = $category->{$key};
        //            $oldIcon = Image::whereKey($oldIconId)->first();
        //
        //            if (!is_null($oldIcon)) {
        //                //$oldIcon->forceDelete();
        //                $oldIcon->delete();
        //            }
        //
        //            $newIcon = Image::whereKey($newIconId)->first();
        //            (is_null($newIcon))
        //                ?: $category->images()->save($newIcon);
        //
        //            $category->{$key} = $newIcon->getKey();
        //        }
        //    }
        //}
        //$category->save();

        $message = [
            'type' => 'success',
            'text' => 'Категория успешно добавлена'
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Category $category) {
        SEOMeta::setTitle($category->getName());
        return view('products.categories.show', compact('category'));
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Category $category) {
        SEOMeta::setTitle($category->getName() . ' - редактирование');
        return view('products.categories.edit', compact('category'));
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function update(Request $request, Category $category) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:categories,name,'.$category->getKey()
        ], [
            'name.unique' => 'Данная категория уже существует'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only(['name','color', 'is_favorite', 'for_blog', 'for_products', 'icon']);
        $category->update($frd);
        $category->updateCategory($frd);

        //
        //if (!isset($frd['is_favorite'])) {
        //    $category->setIsFavorite(false);
        //}
        //
        //if (!isset($frd['for_blog'])) {
        //    $category->setForBlog(false);
        //}
        //
        //if (!isset($frd['for_products'])) {
        //    $category->setForProducts(false);
        //}

        //
        //if (isset($frd['icon']) && !empty($frd['icon'])) {
        //    $icons = $frd['icon'];
        //
        //    foreach ($icons as $key => $newIconId) {
        //        if (isset($newIconId)) {
        //            $oldIconId = $category->{$key};
        //            $oldIcon = Image::whereKey($oldIconId)->first();
        //
        //            if (!is_null($oldIcon)) {
        //                $oldIcon->delete();
        //            }
        //
        //            $newIcon = Image::whereKey($newIconId)->first();
        //            (is_null($newIcon))
        //                ?: $category->images()->save($newIcon);
        //
        //            $category->{$key} = $newIcon->getKey();
        //        }
        //    }
        //}

        $message = [
            'type' => 'success',
            'text' => 'Категория успешно обновлена'
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Category $category) {
        $category->delete();

        $message = [
            'type' => 'success',
            'text' => 'Категория «' . $category->getName() . '» успешно удалена'
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorite(Request $request, Category $category) {
        $frd = $request->only(['is_favorite']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['is_favorite']) && 'on' === $frd['is_favorite']) {
            $category->setIsFavorite(true);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» добавлена в избранное';
        } else {
            $category->setIsFavorite(false);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» удалена из избранного';
        }

        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ordering(Request $request, Category $category) {
        $frd = $request->only(['ordering']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if(empty($frd['ordering'])){
			$frd['ordering'] = 0;
		}

		$category->update($frd);
		$category->save();
		$message['message']['text'] = 'Категория «' . $category->getName() . '» изменена';
        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forProducts(Request $request, Category $category) {
        $frd = $request->only(['for_products']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['for_products']) && 'on' === $frd['for_products']) {
            $category->setForProducts(true);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» установлена для акций';
        } else {
            $category->setForProducts(false);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» откреплена от акций';
        }

        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forBlog(Request $request, Category $category) {
        $frd = $request->only(['for_blog']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['for_blog']) && 'on' === $frd['for_blog']) {
            $category->setForBlog(true);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» установлена для блога';
        } else {
            $category->setForBlog(false);
            $category->save();
            $message['message']['text'] = 'Категория «' . $category->getName() . '» откреплена от блога';
        }

        $response = response()->json($message);

        return $response;
    }
}
