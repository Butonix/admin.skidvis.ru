<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 17:30
 */

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\AuditoryCollection;
use App\Models\Products\Auditory;
use Illuminate\Http\Request;

class AuditoryController extends Controller {
    /**
     * @var Auditory
     */
    protected $auditories;

    /**
     * AuditoryController constructor.
     *
     * @param Auditory $auditories
     */
    public function __construct(Auditory $auditories) {
        $this->auditories = $auditories;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();

        $auditories = $this->auditories->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $this->auditories->getPerPage());

        return response()->json([
            'list' => new AuditoryCollection($auditories),
        ], 200);
    }
}
