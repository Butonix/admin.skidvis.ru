<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller {
    /**
     * @var Tag
     */
    protected $tags;

    /**
     * TagController constructor.
     * @param Tag $tags
     */
    public function __construct(Tag $tags) {
        $this->tags = $tags;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();
        $tags = $this->tags->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $this->tags->getPerPage(), [
            'id',
            'name'
        ]);

        return response()->json([
            'list' => $tags
        ], 200);
    }
}
