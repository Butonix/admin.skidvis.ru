<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 04.06.2019
 * Time: 17:22
 */

namespace App\Http\Controllers\Users;


use App\Http\Controllers\Controller;
use App\Models\Users\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller {
    /**
     * @var Permission
     */
    protected $permissions;

    /**
     * PermissionController constructor.
     * @param Permission $permissions
     */
    public function __construct(Permission $permissions) {
        $this->permissions = $permissions;
        $this->middleware(['role:super_administrator']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request) {
        if (!\Auth::check()) {
            return redirect('/login');
        }

        $frd = $request->only([
            'perPage',
            'search'
        ]);
        $permissions = $this->permissions->filter($frd)
                                         ->orderby('name', 'ASC')
                                         ->paginate($frd['perPage'] ?? $this->permissions->getPerPage());

        return view('permissions.index', compact('permissions', 'frd'));
    }

    /**
     * @param Request $request
     */
    public function create(Request $request) {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name'         => 'required|string|max:255|unique:permissions',
            'display_name' => 'string|max:255',
            'description'  => 'max:255'
        ]);
        $frd = $request->only([
            'name',
            'display_name',
            'description',
            'crud'
        ]);
        if (isset($frd['crud'])) {
            (new Permission)->createCrud($frd['name'], $frd['display_name'], $frd['description']);
            $name = $frd['display_name'] . " - CRUD";
        } else {
            $permission = $this->permissions->create($frd);
            $name = $permission->getDisplayName();
        }

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'permissions') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $permissions = $this->permissions->filter($frd)
                                         ->orderby('name', 'ASC')
                                         ->paginate($frd['perPage'] ?? $this->permissions->getPerPage());
        $html = view('permissions.components._index', compact('permissions', 'frd'))->render();

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Разрешение «' . $name . '» успешно создано.',
            ],
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];
        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with(['flash_message' => $message['message']]);
        }

        return $response;
    }

    /**
     * @param Request    $request
     * @param Permission $permission
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Permission $permission) {
        return view('permissions.show', compact('permission'));
    }

    /**
     * @param Request    $request
     * @param Permission $permission
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Permission $permission) {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * @param Request    $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Permission $permission) {
        $this->validate($request, [
            'name'         => 'required|string|max:255',
            'display_name' => 'string|max:255',
            'description'  => 'string|max:255'
        ]);
        $frd = $request->only([
            'name',
            'display_name',
            'description'
        ]);
        $permission->update($frd);
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Разрешение «' . $permission->getDisplayName() . '» успешно обновлено.',
            ]
        ];

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with(['flash_message' => $message['message']]);
        }

        return $response;
    }

    /**
     * @param Request    $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Permission $permission) {
        $permission->delete();
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Разрешение успешно удалено.',
            ]
        ];

        return response()->json($message);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->all();
        $this->permissions->destroy($frd['permissions']);

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'permissions') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $permissions = $this->permissions->filter($frd)
                                         ->orderby('name', 'ASC')
                                         ->paginate($frd['perPage'] ?? $this->permissions->getPerPage());
        $html = view('permissions.components._index', compact('permissions', 'frd'))->render();

        $flashMessage = [
            'type'    => 'success',
            'text'    => 'Разрешения успешно удалены.',
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];
        $response = response()->json($flashMessage);
        return $response;
    }
}
