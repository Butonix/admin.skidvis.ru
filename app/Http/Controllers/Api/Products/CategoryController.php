<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 27.06.2019
 * Time: 22:58
 */

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\CategoryCollection;
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
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();
        $defaultPerPage = $this->categories->getPerPage();

        if (isset($frd['blog'])) {
            $defaultPerPage = $this->categories->getPerPageForFavoriteBlog();
        }

        $categories = $this->categories->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $defaultPerPage);

        return response()->json([
            'list' => new CategoryCollection($categories),
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
        $image = Category::saveLogo($frd['image']);

        return response()->json([
            'image'  => $image,
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
}
