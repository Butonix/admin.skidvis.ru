<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WelcomeController@welcomePage');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['web']], function () {
    Route::get('policy', 'WelcomeController@policy');
    /**
     * Routes of users
     */
    Route::get('cabinet/edit', 'Cabinet\UserController@edit')->name('cabinet.edit');
    Route::patch('cabinet', 'Cabinet\UserController@update')->name('cabinet.update');
    Route::get('cabinet/password', 'Cabinet\UserController@updatePasswordView')->name('cabinet.update.password.view');
    Route::patch('cabinet/password', 'Cabinet\UserController@updatePassword')->name('cabinet.update.password');

    Route::group(['middleware' => ['authProviderConfig']], function () {
        Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider')->name('auth.social');
        Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback')
             ->name('auth.social.callback');
    });

    Route::middleware(['role:super_administrator'])->group(function () {
        Route::delete('users/destroy', 'Users\UserController@actionsDestroy')->name('users.actions.destroy');
        Route::get('users/export', 'Users\UserController@fullExport')->name('users.export');
        Route::get('users/{user}/roles', 'Users\UserController@roles')->name('users.roles');
        Route::get('users/{user}/organizations', 'Users\UserController@organizations')->name('users.organizations');
        Route::get('users/{user}/permissions', 'Users\UserController@permissions')->name('users.permissions');
        Route::patch('users/{user}/permissions-update', 'Users\UserController@permissionsUpdate')
             ->name('users.permissions.update');
        Route::patch('users/{user}/roles-update', 'Users\UserController@rolesUpdate')->name('users.roles.update');
        Route::patch('users/{user}/organizations-update', 'Users\UserController@organizationsUpdate')
             ->name('users.organizations.update');
        Route::get('users/{user}/edit/password', 'Users\UserController@updatePasswordView')
             ->name('users.edit.password');
        Route::patch('users/{user}/edit/password', 'Users\UserController@updatePassword')
             ->name('users.edit.password.update');
        Route::resource('users', 'Users\UserController');

        Route::delete('roles/destroy', 'Users\RoleController@actionsDestroy')->name('roles.actions.destroy');
        Route::get('roles/{role}/permissions', 'Users\RoleController@permissions')->name('roles.permissions');
        Route::patch('roles/{role}/permissions-update', 'Users\RoleController@permissionsUpdate')
             ->name('roles.permissions.update');
        Route::resource('roles', 'Users\RoleController');

        Route::delete('permissions/destroy', 'Users\PermissionController@actionsDestroy')
             ->name('permissions.actions.destroy');
        Route::resource('permissions', 'Users\PermissionController');

        Route::delete('social-networks/destroy', 'Social\SocialNetworkController@actionsDestroy')
             ->name('social-networks.actions.destroy');
        Route::resource('social-networks', 'Social\SocialNetworkController')->parameters([
            'social-networks' => 'socialNetwork',
        ]);

        Route::patch('auth-providers/{provider}/published', 'Auth\AuthProviderController@setPublished')
             ->name('providers.set.published');
        Route::delete('auth-providers/destroy', 'Auth\AuthProviderController@actionsDestroy')
             ->name('providers.actions.destroy');
        Route::resource('auth-providers', 'Auth\AuthProviderController')->parameters([
            'auth-providers' => 'provider',
        ]);;
    });

    Route::get('organizations/{organization}', 'Organizations\OrganizationController@show')->name('organizations.show');

    Route::prefix('management')->group(function () {
        Route::middleware(['role:super_administrator|administrator|management'])->group(function () {
            //Route::resource('organizations/{organization}/products', 'Products\ProductController');
            //Route::resource('organizations', 'Organizations\OrganizationController');
            //Route::resource('organizations/{organization}/points', 'Organizations\PointController');
        });

        Route::middleware(['role:super_administrator'])->group(function () {
            Route::get('social-networks/all', 'Social\SocialNetworkController@allSocialNetworksJson')
                 ->name('social-networks.all.json');
            Route::delete('article-labels/destroy', 'Articles\ArticleLabelController@actionsDestroy')
                 ->name('article-labels.action.destroy');
            Route::resource('article-labels', 'Articles\ArticleLabelController')->parameters([
                'article-labels' => 'label',
            ]);

            Route::patch('notifications/{notification}/read', 'Notifications\NotificationController@makeRead')->name('notifications.make-as-read');
            Route::resource('notifications', 'Notifications\NotificationController')->only(['index', 'destroy']);
        });

        Route::resource('articles', 'Articles\ArticleController');

        Route::delete('tags/destroy', 'Products\TagController@actionsDestroy')->name('tags.action.destroy');
        Route::resource('tags', 'Products\TagController');

        Route::patch('auditories/{auditory}/favorite', 'Products\AuditoryController@favorite')
             ->name('auditories.favorite');
        Route::resource('auditories', 'Products\AuditoryController');

        Route::patch('holidays/{holiday}/favorite', 'Products\HolidayController@favorite')
             ->name('holidays.favorite');
        Route::resource('holidays', 'Products\HolidayController');

        Route::patch('categories/{category}/favorite', 'Products\CategoryController@favorite')
             ->name('categories.favorite');

        Route::patch('categories/{category}/ordering', 'Products\CategoryController@ordering')
             ->name('categories.ordering');
        Route::patch('categories/{category}/for-products', 'Products\CategoryController@forProducts')
             ->name('categories.for-products');
        Route::patch('categories/{category}/for-blog', 'Products\CategoryController@forBlog')
             ->name('categories.for-blog');
        Route::resource('categories', 'Products\CategoryController');
    });

    //Route::get('products', 'Products\ProductController@allProducts')->name('products.all');
    //Route::get('products/{product}', 'Products\ProductController@productsShow')->name('products.showing');
});

Route::get('/teee', 'Api\Organizations\OrganizationController@store');
Route::get('/testsss', 'WelcomeController@test');
Route::get('mail', 'HomeController@mail');
