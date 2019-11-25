<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 04.06.2019
 * Time: 17:22
 */

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Organizations\Organization;
use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Models\Users\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller {
    /**
     * @var User
     */
    protected $users;

    /**
     * @var Role
     */
    protected $roles;

    /**
     * @var Permission
     */
    protected $permissions;

    /**
     * @var Organization
     */
    protected $organizations;

    /**
     * UserController constructor.
     *
     * @param User $users
     */
    public function __construct(User $users, Role $roles, Permission $permissions, Organization $organizations) {
        $this->users = $users;
        $this->roles = $roles;
        $this->permissions = $permissions;
        $this->organizations = $organizations;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request) {
        if (!\Auth::check()) {
            return redirect('/login');
        }

        SEOMeta::setTitle('Пользователи');
        $frd = $request->only([
            'perPage',
            'search',
            'role_id',
            'permission_id',
        ]);
        $users = $this->users->filter($frd)
                             ->orderBy('l_name', 'ASC')
                             ->with(['roles', 'roles.permissions'])
                             ->paginate($frd['perPage'] ?? $this->users->getPerPage());
        $rolesList = $this->roles->usingRolesList();
        $permissionsList = $this->permissions->usingPermissionsInRolesList();

        return view('users.index', compact('frd', 'users', 'rolesList', 'permissionsList'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        $rolesList = Role::getRolesList();
        SEOMeta::setTitle('Создание пользователя');

        return view('users.create', compact('rolesList'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request) {
        $this->validate($request, [
            'f_name'   => 'required|string|max:255',
            'l_name'   => 'nullable|max:255',
            'm_name'   => 'nullable|max:255',
            'phone'    => 'nullable|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $frd = $request->only([
            'f_name',
            'l_name',
            'm_name',
            'phone',
            'email',
            'password',
            'roles'
        ]);

        $password = $frd['password'];
        $frd['password'] = Hash::make($password);
        $user = $this->users->create($frd);

        if (isset($frd['roles']) && !empty($frd['roles'])) {
            $user->attachRoles($frd['roles']);
            $user->save();
        }

        $message = [
            'type' => 'success',
            'text' => 'Пользователь «' . $user->getName() . '» успешно создан',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request $request
     * @param User    $user
     */
    public function show(Request $request, User $user) {

    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, User $user) {
        SEOMeta::setTitle($user->getName() . ' - редактирование');

        return view('users.edit', compact('user'));
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user) {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|string|max:255',
            'l_name' => 'nullable|max:255',
            'm_name' => 'nullable|max:255',
            'email'  => 'required|string|max:255|unique:users,email,' . $user->getKey(),
            'phone'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only([
            'f_name',
            'l_name',
            'm_name',
            'email',
            'phone',
        ]);
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Профиль пользователя «' . $user->getName() . '» успешно обновлен',
            ],
        ];

        $editUserRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();

        if (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($editUserRoles)) {
            $user->update($frd);
        } else {
            $message = [
                'message' => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав',
                ],
            ];
        }

        return response()->json($message);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, User $user) {
        $message = [
            'type' => 'success',
            'text' => 'Профиль пользователя успешно удален.',
        ];

        $editUserRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();

        if (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($editUserRoles)) {
            $user->delete();
        } else {
            $message = [
                'type' => 'error',
                'text' => 'Недостаточно прав',
            ];
        }

        $response = redirect()->back()->with(['flash_message' => $message]);

        return $response;
    }

    /**
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions(Request $request, User $user) {
        SEOMeta::setTitle($user->getName() . ' - разрешения');
        $frd = $request->all();

        $userPermissionsOfRoles = $user->allPermissionsOfRoles();
        $permissionsCount = $user->getPermissionsCount();
        $userPermissions = $user->getPermissions();
        $userPermissionsIds = $user->getPermissionsIds();
        $permissions = $this->permissions->permissionsWithout($userPermissionsIds)
                                         ->filter($frd ?? [])
                                         ->orderby('name', 'ASC')
                                         ->paginate($frd['perPage'] ?? $this->roles->getPerPage());

        return view('users.permissions', compact('frd', 'userPermissionsOfRoles', 'user', 'permissionsCount', 'userPermissions', 'permissions'));
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function permissionsUpdate(Request $request, User $user) {
        $userRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();
        $frd = $request->only(['permissions']);
        $permissionsDetach = [];
        $permissionsAttach = [];
        $flashMessage = [
            'message' => [
                'type' => 'error',
                'text' => 'Изменение разрешений недоступно',
            ],
        ];

        if (isset($frd['permissions']['off'])) {
            $permissionsDetach = array_keys($frd['permissions']['off']);
        }
        if (isset($frd['permissions']['on'])) {
            $permissionsAttach = array_keys($frd['permissions']['on']);
        }

        if (auth()->id() === $user->getKey() && Auth::user()->canEditOwnRoles()) {
            $flashMessage = $this->makingPermissions($permissionsDetach, $permissionsAttach, $user);
        } elseif (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($userRoles) && Auth::user()
                                                                                                         ->catAttachRole($permissionsAttach)) {
            $flashMessage = $this->makingPermissions($permissionsDetach, $permissionsAttach, $user);
        }

        if ($request->ajax()) {
            $response = response()->json($flashMessage);
        } else {
            $response = redirect()->back()->with(['flash_message' => $flashMessage]);
        }
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function roles(Request $request, User $user) {
        SEOMeta::setTitle($user->getName() . ' - роли');
        $frd = $request->only([
            'search',
        ]);
        $rolesCount = $user->getRolesCount();
        $userRoles = $user->getRolesCollection();
        $userRolesIds = $user->getRolesIds();
        $roles = $this->roles->rolesWithout($userRolesIds)
                             ->filter($frd ?? [])
                             ->orderby('name', 'ASC')
                             ->paginate($frd['perPage'] ?? $this->roles->getPerPage());

        return view('users.roles', compact('roles', 'user', 'frd', 'rolesCount', 'userRoles'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function rolesUpdate(Request $request, User $user) {
        $userRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();
        $frd = $request->only(['roles']);
        $rolesDetach = [];
        $rolesAttach = [];
        $flashMessage = [
            'message' => [
                'type' => 'error',
                'text' => 'Изменение прав недоступно',
            ],
        ];

        if (isset($frd['roles']['off'])) {
            $rolesDetach = array_keys($frd['roles']['off']);
        }
        if (isset($frd['roles']['on'])) {
            $rolesAttach = array_keys($frd['roles']['on']);
        }

        if (auth()->id() === $user->getKey() && Auth::user()->canEditOwnRoles()) {
            $flashMessage = $this->makingRoles($rolesDetach, $rolesAttach, $user);
        } elseif (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($userRoles) && Auth::user()
                                                                                                         ->catAttachRole($rolesAttach)) {
            $flashMessage = $this->makingRoles($rolesDetach, $rolesAttach, $user);
        }

        if ($request->ajax()) {
            $response = response()->json($flashMessage);
        } else {
            $response = redirect()->back()->with(['flash_message' => $flashMessage['message']]);
        }
        return $response;
    }

    /**
     * @param array $permissionsDetach
     * @param array $permissionsAttach
     * @param       $user
     *
     * @return array
     */
    protected function makingPermissions(array $permissionsDetach, array $permissionsAttach, User $user): array {
        $user->detachPermissions($permissionsDetach);
        $user->attachPermissions($permissionsAttach);

        $flashMessage = [
            'message' => [
                'type' => 'success',
                'text' => 'Разрешения для пользователя «' . $user->getName() . '» успешно обновлены',
            ],
        ];

        return $flashMessage;
    }

    /**
     * @param array $rolesDetach
     * @param array $rolesAttach
     * @param       $user
     *
     * @return array
     */
    protected function makingRoles(array $rolesDetach, array $rolesAttach, User $user): array {
        $user->detachRoles($rolesDetach);
        $user->attachRoles($rolesAttach);

        $flashMessage = [
            'message' => [
                'type' => 'success',
                'text' => 'Роли для пользователя «' . $user->getName() . '» успешно обновлены',
            ],
        ];

        return $flashMessage;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function actionsDestroy(Request $request) {
        $frd = $request->only([
            'users',
        ]);
        $usersForDelete = [];
        $canDelete = true;
        $flashMessage = [
            'type' => 'success',
            'text' => 'Пользователи успешно удалены.',
        ];

        foreach ($frd['users'] as $userId) {
            $user = User::whereKey($userId)->first();
            $editUserRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();

            if (\auth()->id() === $user->getKey()) {
                continue;
            } elseif (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($editUserRoles)) {
                $usersForDelete[] = $user->getKey();
            } else {
                $flashMessage = [
                    'type' => 'error',
                    'text' => 'Недостаточно прав',
                ];
                $canDelete = false;
                break;
            }
        }

        if ($canDelete) {
            $this->users->destroy($usersForDelete);
            $frdSearch = [];
            foreach ($frd as $key => $value) {
                if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                    $frdSearch[$key] = $value;
                }
            }
            $frd = $frdSearch;
            $users = $this->users->filter($frd)->paginate($frd['perPage'] ?? $this->users->getPerPage());
            $rolesList = $this->roles->usingRolesList();
            $permissionsList = $this->permissions->usingPermissionsInRolesList();
            $html = view('users.components._index', compact('users', 'frd', 'rolesList', 'permissionsList'))->render();
            $flashMessage['replace'] = [
                'selector' => '.js-index',
                'html'     => $html,
            ];
        }

        $response = response()->json($flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function fullExport(Request $request) {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Encoding'    => 'UTF-8',
            'Content-Disposition' => 'attachment; filename=events-' . str_slug(date('Y-m-d H:i:s')) . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
        ];

        $frd = $request->all();
        $list = array();
        $list[] = [
            'id',
            'Фамилия',
            'Имя',
            'Отчество',
            'Телефон',
            'Email',
        ];

        $users = $this->users::orderBy('id', 'ASC')->get();
        foreach ($users as $user) {
            $userData = [
                $user->getKey(),
                $user->getLastName(),
                $user->getFirstName(),
                $user->getMiddleName(),
                $user->getPhone(),
                $user->getEmail(),
            ];
            $list[] = $userData;
        }

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'wb');
            fprintf($FH, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($list as $row) {
                fputcsv($FH, $row, ',');
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request, $user) {
        $user = $this->users->where('id', $user)->first();
        $password = Str::random(12);
        $passwordHash = Hash::make($password);
        $user->update([
            'password' => $passwordHash,
        ]);
        $user->save();

        //$user->notify(new ChangedPassword($password));

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Пароль для «' . $user->getName() . '» успешно обновлён',
            ],
        ];

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with('message', $message);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updatePasswordView(Request $request, User $user) {
        return view('users.updatePassword', compact('user'));
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $user) {
        $frd = $request->only(['password']);
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Пароль успешно обновлён',
            ],
        ];

        $editUserRoles = $user->roles()->pluck('roles.name', 'roles.id')->toArray();
        if (auth()->id() !== $user->getKey() && Auth::user()->canEditUsersRoles($editUserRoles)) {
            $passwordHash = Hash::make($frd['password']);
            $user->update([
                'password' => $passwordHash,
            ]);
            $user->save();

            //$user->notify(new ChangedPassword($frd['password']));
        } else {
            $message = [
                'message' => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав',
                ],
            ];
        }

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with('flash_message', $message['message']);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function organizations(Request $request, User $user) {
        SEOMeta::setTitle($user->getName() . ' - организации');
        $frd = $request->all();
        $userOrganizations = $user->getOrganizations();
        $userOrganizationsCount = $user->getOrganizationsCount();
        $userOrganizationsIds = $user->getOrganizationsIds();
        $organizations = $this->organizations->organizationsWithout($userOrganizationsIds)
                                             ->filter($frd)
                                             ->ordering($frd)
                                             ->with(['phone', 'email'])
                                             ->paginate($frd['perPage'] ?? $this->organizations->getPerPage());

        return view('users.organizations', compact('frd', 'user', 'organizations', 'userOrganizations', 'userOrganizationsCount'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function organizationsUpdate(Request $request, User $user) {
        $frd = $request->only(['organizations']);
        $organizationsDetach = [];
        $organizationsAttach = [];

        if (isset($frd['organizations']['off'])) {
            $organizationsDetach = array_keys($frd['organizations']['off']);
        }
        if (isset($frd['organizations']['on'])) {
            $organizationsAttach = array_keys($frd['organizations']['on']);
        }

        $flashMessage = $this->makingOrganizations($organizationsDetach, $organizationsAttach, $user);

        if ($request->ajax()) {
            $response = response()->json($flashMessage);
        } else {
            $response = redirect()->back()->with(['flash_message' => $flashMessage['message']]);
        }

        return $response;
    }

    /**
     * @param array $organizationsDetach
     * @param array $organizationsAttach
     * @param User  $user
     *
     * @return array
     */
    protected function makingOrganizations(array $organizationsDetach, array $organizationsAttach, User $user): array {
        $user->organizations()->detach($organizationsDetach);
        $user->organizations()->attach($organizationsAttach);
        $user->save();

        $flashMessage = [
            'message' => [
                'type' => 'success',
                'text' => 'Организации для пользователя «' . $user->getName() . '» успешно обновлены',
            ],
        ];

        return $flashMessage;
    }
}
