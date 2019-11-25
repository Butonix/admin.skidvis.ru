<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 04.06.2019
 * Time: 17:22
 */

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Models\Users\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * UserController constructor.
     * @param User $users
     */
    public function __construct(User $users, Role $roles, Permission $permissions) {
        $this->users = $users;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request) {

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
            'f_name'   => 'required|string|max:255',
            'l_name'   => 'max:255',
            'm_name'   => 'max:255',
            'phone'    => 'string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $frd = $request->only([
            'f_name',
            'l_name',
            'm_name',
            'phone',
            'email',
            'password'
        ]);
        $password = $frd['password'];
        $frd['password'] = Hash::make($password);
        $user = $this->users->create($frd);

        $frdSearch = [];
        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }
        $frd = $frdSearch;
        $users = $this->users->filter($frd)
                             ->orderBy('l_name', 'ASC')
                             ->paginate($frd['perPage'] ?? $this->users->getPerPage());
        $rolesList = $this->roles->usingRolesList();
        $permissionsList = $this->permissions->usingPermissionsInRolesList();
        $html = view('users.components._index', compact('users', 'frd', 'rolesList', 'permissionsList'))->render();

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Пользователь «' . $user->getName() . '» успешно создан',
            ],
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html
            ]
        ];

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with(['message' => $message['message']]);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request) {
        SEOMeta::setTitle('Кабинет');

        $user = \auth()->user();
        return view('users.edit', compact('user'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request) {
        $user = Auth::user();
        $this->validate($request, [
            'f_name' => 'required|string|max:255',
            'l_name' => 'max:255',
            'm_name' => 'max:255',
            'email'  => 'required|string|max:255',
            'phone'  => 'string|max:255',
        ]);
        $frd = $request->only([
            'f_name',
            'l_name',
            'm_name',
            'email',
            'phone',
        ]);
        $user->update($frd);

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Профиль пользователя «' . $user->getName() . '» успешно обновлен',
            ]
        ];

        return response()->json($message);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updatePasswordView(Request $request) {
        $user = Auth::user();
        SEOMeta::setTitle($user->getName() . ' - обновление пароля');

        return view('users.updatePassword', compact('user'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request) {
        $user = Auth::user();
        $frd = $request->only(['password']);
        $passwordHash = Hash::make($frd['password']);
        $user->update([
            'password' => $passwordHash
        ]);
        $user->save();

        //$user->notify(new ChangedPassword($frd['password']));

        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Пароль успешно обновлён',
            ],
        ];

        if ($request->ajax()) {
            $response = response()->json($message);
        } else {
            $response = redirect()->back()->with('flash_message', $message['flash_message']);
        }

        return $response;
    }
}
