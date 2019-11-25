<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        /**
         * ---------------------------------
         * Роуты, связанные с пользователем
         * ---------------------------------
         */
        Route::post('user/city', 'Api\Cabinet\UserController@cityStore')->name('user.city');
        Route::get('user', 'Api\Cabinet\UserController@user');

        Route::get('user/bookmarks', 'Api\Bookmarks\BookmarkController@index')->name('bookmarks.index');
        Route::post('user/bookmarks', 'Api\Bookmarks\BookmarkController@store')->name('bookmarks.store');

        Route::get('user/wishlist', 'Api\Cabinet\UserController@wishlistShow')->name('user.wishlist.show');
        Route::post('user/wishlist', 'Api\Cabinet\UserController@wishlistStore')->name('user.wishlist.store');

        Route::get('user/refresh', 'Api\Auth\LoginController@refresh')->name('user.token.refresh');
        Route::post('logout', 'Api\Auth\LoginController@logout');
        Route::get('email/resend', 'Api\Auth\VerificationController@resend')->name('email.resend');
        Route::get('email/verify', 'Api\Auth\VerificationController@show')->name('email.show');
        Route::get('email/verify/{id}', 'Api\Auth\VerificationController@verify')->name('email.verify');
        Route::post('settings/avatar', 'Api\Cabinet\UserController@avatar');
        Route::patch('settings/profile', 'Api\Cabinet\UserController@update');
        Route::patch('settings/password', 'Api\Cabinet\UserController@updatePassword');

        /**
         * -------------------------------------------------------
         * Роуты для отметки/удаления отметки избранного продукта/статьи
         * -------------------------------------------------------
         */
        Route::post('products/{product}/marked', 'Api\Products\ProductController@setMarkedProduct')
             ->name('products.set.marked');
        Route::delete('products/{product}/marked', 'Api\Products\ProductController@deleteMarkedProduct')
             ->name('products.delete.marked');
        Route::post('articles/{article}/marked', 'Api\Organizations\ArticleController@setMarkedArticle')
             ->name('articles.set.marked');
        Route::delete('articles/{article}/marked', 'Api\Organizations\ArticleController@deleteMarkedArticle')
             ->name('articles.delete.marked');

        /**
         * -----------------------------------------
         * Роуты для работы с отзывами и их лайками
         * -----------------------------------------
         */
        Route::get('reviews/{review}/like', 'Api\Reviews\ReviewController@likeReview')->name('reviews.like');
        Route::get('reviews/{review}/unlike', 'Api\Reviews\ReviewController@unlikeReview')->name('reviews.unlike');

        Route::post('articles/{article}/reviews', 'Api\Reviews\ReviewController@storeForArticle')
             ->name('articles.reviews.store');

        Route::post('products/{product}/reviews', 'Api\Reviews\ReviewController@storeForProduct')
             ->name('products.reviews.store');

        Route::post('organizations/{organization}/reviews', 'Api\Reviews\ReviewController@storeForOrganization')
             ->name('organizations.reviews.store');

        Route::middleware(['role:super_administrator|administrator'])
             ->group(function () {
                 Route::delete('reviews/{review}', 'Api\Reviews\ReviewController@destroy')->name('reviews.destroy');
             });


        /**
         * --------------------------------------
         * Роуты для менежеров и администраторов
         * --------------------------------------
         */
        Route::prefix('management')->group(function () {
            Route::middleware(['role:super_administrator|administrator|management'])
                 ->group(function () {
                     Route::patch('products/{product}/unpublish', 'Api\Products\ProductController@unpublish')
                          ->name('products.unpublish');
                     Route::post('products/image', 'Api\Products\ProductController@image'); //Saving main image for organizations (cover) without relations

					 Route::middleware(['role:super_administrator'])->
					 patch('organizations/{organization}/services', 'Api\Organizations\OrganizationController@servicesUpdate')
						 ->name('organizations.services.update');

                     Route::resource('organizations/{organization}/products', 'Api\Products\ProductController');

                     Route::post('organizations/{organization}/points/check', 'Api\Organizations\PointController@checkBeforeStore')->name('organizations.points.check');
                     Route::post('organizations/{organization}/points/import/simple', 'Api\Organizations\PointController@importSimple')->name('organizations.points.import.simple');
                     Route::resource('organizations/{organization}/points', 'Api\Organizations\PointController');

                     Route::resource('organizations', 'Api\Organizations\OrganizationController');
                 });

            Route::middleware(['role:super_administrator|administrator'])->group(function () {
                Route::patch('organizations/{organization}/unpublish', 'Api\Organizations\OrganizationController@unpublish')
                     ->name('organizations.unpublish');
                //Route::get('organizations/{organization}/users', 'Api\Organizations\OrganizationController@users')
                //     ->name('organizations.users');

                //Сохранение мини-лого для организаций (без прикрепрения, просто сохранение)
                Route::post('organizations/mini-logo', 'Api\Organizations\OrganizationController@miniLogo')
                     ->name('organizations.mini-logo.store');

                //Сохранение лого для организаций (без прикрепрения, просто сохранение)
                Route::post('organizations/logo', 'Api\Organizations\OrganizationController@logo')
                     ->name('organizations.logo.store');

                //Сохранение обложек для организаций (без прикрепрения, просто сохранение)
                Route::post('organizations/image', 'Api\Organizations\OrganizationController@image')
                     ->name('organizations.image.store');

            });
        });
    });

    /**
     * ----------------------------------------------
     * Роуты для авторизации и восстановления пароля
     * ----------------------------------------------
     */
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('login', 'Api\Auth\LoginController@login');
        Route::post('register', 'Api\Auth\RegisterController@register');

        Route::post('password/email', 'Api\Auth\ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'Api\Auth\ResetPasswordController@reset');
        Route::get('password/reset', 'Api\Auth\ResetPasswordController@showResetForm')->name('api.password.reset');

        Route::group(['middleware' => ['apiAuthProviderConfig']], function () {
            Route::post('oauth/{provider}', 'Api\Auth\LoginController@redirectToProvider')->name('auth.social');
        });
    });

	Route::get('map/icon/{color?}', 'Api\Organizations\PointController@mapIcon')->name('map.icon');

    /**
     * -------------------------------------
     * Роуты для работы с организациями
     * и их акциями для всех пользователей
     * -------------------------------------
     */
    Route::get('articles/latest', 'Api\Organizations\ArticleController@latest')->name('articles.latest');
    Route::get('articles', 'Api\Organizations\ArticleController@index')->name('articles.index');
    Route::get('articles/{article}', 'Api\Organizations\ArticleController@show')->name('articles.show');
	Route::get('articles/{article}/reviews', 'Api\Organizations\ArticleController@reviews')->name('articles.reviews');
    Route::post('feedbacks', 'Api\Feedbacks\FeedbackController@store')->name('feedbacks.store');
    Route::get('cities/{city}', 'Api\Cities\CityController@show')->name('cities.show');
    Route::get('cities', 'Api\Cities\CityController@allCities')->name('cites.all');
    Route::get('react-data', 'Api\BreadcrumbController@reactData')->name('breadcrumbs.react-data');
    Route::get('tags', 'Api\Products\TagController@index')->name('tags.index');
    Route::get('categories', 'Api\Products\CategoryController@index')->name('categories.index');
    Route::get('auditories', 'Api\Products\AuditoryController@index')->name('auditories.index');
    Route::get('holidays', 'Api\Products\HolidayController@index')->name('holidays.index');
    Route::get('timezones', 'Api\Cities\CityController@index')->name('timezones.index');

    Route::get('points/map', 'Api\Organizations\PointController@map')->name('points.map');
    Route::get('products/map', 'Api\Products\ProductController@map')->name('products.map');

    Route::get('products', 'Api\Products\ProductController@allProducts')->name('products.pub.all');
    Route::get('products/{product}/points-different-time', 'Api\Products\ProductController@pointsDifferentTime')
         ->name('products.points.different-time');
    Route::get('products/{product}/reviews', 'Api\Products\ProductController@reviews')->name('products.reviews');
    Route::get('products/{product}', 'Api\Products\ProductController@productsShow')->name('products.pub.show');
    Route::get('products/{product}/map', 'Api\Products\ProductController@productsShowMap')->name('products.map');

    Route::get('organizations', 'Api\Organizations\OrganizationController@all')->name('organizations.pub.all');
    Route::get('organizations/{organization}', 'Api\Organizations\OrganizationController@organizationsShow')
         ->name('organizations.pub.show');
    Route::get('organizations/{organization}/reviews', 'Api\Organizations\OrganizationController@reviews')
         ->name('organizations.reviews');
    Route::get('organizations/{organization}/products', 'Api\Organizations\OrganizationController@products')
         ->name('organizations.products');
});


Route::group(['middleware' => 'guest:api'], function () {
    Route::group(['middleware' => ['apiAuthProviderConfig']], function () {
        Route::get('auth/{provider}/callback', 'Api\Auth\LoginController@handleProviderCallback')
             ->name('auth.social.callback');
    });

    //Url для сохранения главного изображения статьи, через тех.админку, без авторизации
    Route::post('articles/image', 'Api\Organizations\ArticleController@image')->name('articles.image.store');

    //Url для сохранения изображений в тексте, через тех.админку, без авторизации
    Route::post('articles/text-image', 'Api\Organizations\ArticleController@textImages')
         ->name('articles.text-image.store');

    //Url для сохранения иконок категорий, через тех.админку, без авторизации
    Route::post('categories/image', 'Api\Products\CategoryController@image')->name('categories.image.store');
});
