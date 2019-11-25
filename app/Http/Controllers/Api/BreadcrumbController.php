<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.07.2019
 * Time: 14:25
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Organizations\Organization;
use App\Models\Products\Product;
use Illuminate\Http\Request;

class BreadcrumbController extends Controller {
    public function __construct() {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reactData(Request $request) {
        $frd = $request->only(['organizationId', 'productId', 'articleId']);
        $breadcrumbs = [];

        foreach ($frd as $key => $value) {
            if ($key === 'organizationId' && !is_null($value)) {
                /**
                 * @var Organization $organization
                 */
                $organization = Organization::whereKey((int)$value)->first();
                $breadcrumbs[$key][$value]['name'] = ($organization) ? $organization->getName() : null;
            }

            if ($key === 'productId' && !is_null($value)) {
                /**
                 * @var Product $product
                 */
                $product = Product::whereKey((int)$value)->first();
                $breadcrumbs[$key][$value]['name'] = ($product) ? $product->getName() : null;
            }

            if ($key === 'articleId' && !is_null($value)) {
                /**
                 * @var Article $article
                 */
                $article = Article::whereKey((int)$value)->first();
                $breadcrumbs[$key][$value]['name'] = ($article) ? $article->getName() : null;
            }
        }

        return response()->json($breadcrumbs, 200);
    }
}
