<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 04.06.2019
 * Time: 17:22
 */

namespace App\Http\Controllers\Api\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\ProductCollection;
use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Products\Product;
use App\Models\Users\User;
use App\Http\Resources\Users\User as UserResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    /**
     * @var User
     */
    protected $users;

    /**
     * @var Product
     */
    protected $products;

    /**
     * UserController constructor.
     *
     * @param User $users
     */
    public function __construct(User $users, Product $products) {
        $this->users = $users;
        $this->products = $products;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'f_name'     => 'required|string|max:255',
            'l_name'     => 'nullable|string|max:255',
            'm_name'     => 'nullable|string|max:255',
            'email'      => 'required|string|max:255',
            'phone'      => 'nullable|string|max:255',
            'avatar.id'  => 'nullable|integer',
            'avatar.src' => 'nullable|string',
        ], [
            'f_name.required'   => 'Укажите ваше имя',
            'email.required'    => 'Укажите ваш Email',
            'password.required' => 'Укажите ваш пароль',
            'password.min'      => 'Длина пароля не менее :min символов',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['f_name', 'l_name', 'm_name', 'email', 'phone', 'avatar']);
        /**
         * @var User $user
         */
        $user = $request->user();
        $user->update($frd);
        $user->updateAvatar($frd['avatar'] ?? []);
        $user->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Пользователь успешно обновлен',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function user(Request $request) {
        return response()->json(new UserResource(Auth::guard('api')->user()));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function wishlistStore(Request $request) {
        /**
         * @var User $user
         */
        $frd = $request->all();
        $user = Auth::guard('api')->user();
        Bookmark::saveProducts($frd['products'] ?? [], $user);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Избранное успешно связано с аккаунтом',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function wishlistShow(Request $request) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();
        $frd = $request->all();

        $response = $user->getBookmarksProductsItems($frd);

        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function avatar(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['image']);
        $image = User::saveLogo($frd['image']);

        return response()->json([
            'image'  => $image,
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Аватар успешно сохранен.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cityStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['city_id']);

        /**
         * @var User $user
         */
        $user = Auth::guard('api')->user();
        $user->city()->associate($frd['city_id']);
        $user->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Город успешно изменен',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }
}
