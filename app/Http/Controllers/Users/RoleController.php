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
use App\Models\Users\Role;
use Illuminate\Http\Request;

class RoleController extends Controller {
    /**
     * @var Permission
     */
    protected $permissions;

    /**
     * @var Role
     */
    protected $roles;

    /**
     * /**
     * RoleController constructor.
     * @param Role $roles
     */
    public function __construct(Role $roles, Permission $permissions) {
        $this->roles = $roles;
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

        $frd = $request->all();
        $roles = $this->roles->filter($frd)
                             ->orderby('name', 'ASC')
                             ->paginate($frd['perPage'] ?? $this->roles->getPerPage());

        return view('roles.index', compact('roles', 'frd'));
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
            'name'         => 'required|string|max:255|unique:roles',
            'display_name' => 'string|max:255',
            'description'  => 'max:255'
        ]);
        $frd = $request->all();
        $role = $this->roles->create($frd);

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'roles') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $roles = $this->roles->filter($frd)
                             ->orderby('name', 'ASC')
                             ->paginate($frd['perPage'] ?? $this->roles->getPerPage());
        $html = view('roles.components._index', compact('roles', 'frd'))->render();

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Роль «' . $role->getDisplayName() . '» успешно создана.',
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
     * @param Request $request
     * @param Role    $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Role $role) {
        return view('roles.show', compact('role'));
    }

    /**
     * @param Request $request
     * @param Role    $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Role $role) {
        return view('roles.edit', compact('role'));
    }

    /**
     * @param Request $request
     * @param Role    $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Role $role) {
        $this->validate($request, [
            'name'         => 'required|string|max:255',
            'display_name' => 'string|max:255',
            'description'  => 'string|max:255'
        ]);
        $frd = $request->all();
        $role->update($frd);
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Роль «' . $role->getDisplayName() . '» успешно изменена.',
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
     * @param Request $request
     * @param Role    $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Role $role) {
        $this->roles->destroy($role->getKey());
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Роль успешно удалена.',
            ]
        ];
        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->all();
        $this->roles->destroy($frd['roles']);

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'roles') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $roles = $this->roles->filter($frd)
                             ->orderby('name', 'ASC')
                             ->paginate($frd['perPage'] ?? $this->roles->getPerPage());
        $html = view('roles.components._index', compact('roles', 'frd'))->render();

        $flashMessage = [
            'type'    => 'success',
            'text'    => 'Роли успешно удалены',
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];
        $response = response()->json($flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions(Request $request, Role $role) {
        $frd = $request->all();
        $permissions = $this->permissions->filter($frd)
                                         ->orderby('name', 'ASC')
                                         ->paginate($frd['perPage'] ?? $this->roles->getPerPage())
                                         ->appends($frd);

        return view('roles.permissions', compact('permissions', 'role', 'frd'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function permissionsUpdate(Request $request, Role $role) {
        $frd = $request->only(['permissions']);
        if (isset($frd['permissions']['off'])) {
            $role->detachPermissions(array_keys($frd['permissions']['off']));
        }
        if (isset($frd['permissions']['on'])) {
            $role->attachPermissions(array_keys($frd['permissions']['on']));
        }
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Разрешения для роли «' . $role->getDisplayName() . '» успешно обновлены',
            ]
        ];

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with(['flash_message' => $message['message']]);
        }
        return $response;
    }
}
