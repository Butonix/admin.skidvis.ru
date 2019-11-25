<?php

use App\Models\Users\User;
use App\Models\Users\Role;
use App\Models\Users\Permission;
use App\Models\Organizations\Organization;
use App\Models\Organizations\Point;
use App\Models\Products\Tag;
use App\Models\Products\Auditory;
use App\Models\Products\Holiday;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Models\Social\SocialNetwork;
use App\Models\Articles\Article;
use App\Models\Articles\ArticleLabel;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

try {
    Breadcrumbs::for('home', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->push('Главная', route('home'));
    });

    Breadcrumbs::for('policy', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->push('Политика конфиденциальности', route('policy'));
    });

    Breadcrumbs::for('notifications.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Уведомления', route('notifications.index'));
    });

    Breadcrumbs::for('organizations.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Организации', route('organizations.index'));
    });

    Breadcrumbs::for('organizations.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('organizations.index');
        $breadcrumbs->push('Создание организации', route('organizations.create'));
    });

    Breadcrumbs::for('organizations.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('organizations.index');
        $breadcrumbs->push($organization->getName(), route('organizations.show', $organization));
    });

    Breadcrumbs::for('organizations.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('organizations.show', $organization);
        $breadcrumbs->push('Редактирование', route('organizations.edit', $organization));
    });

    Breadcrumbs::for('points.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('organizations.show', $organization);
        $breadcrumbs->push('Точки', route('points.index', $organization));
    });

    Breadcrumbs::for('points.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('points.index', $organization);
        $breadcrumbs->push('Создание точки', route('points.create', $organization));
    });

    Breadcrumbs::for('points.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization, Point $point) {
        $breadcrumbs->parent('points.index', $organization);
        $breadcrumbs->push($point->getName(), route('points.show', [$organization, $point]));
    });

    Breadcrumbs::for('points.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization, Point $point) {
        $breadcrumbs->parent('points.show', $organization, $point);
        $breadcrumbs->push('Редактирование', route('points.edit', [$organization, $point]));
    });

    Breadcrumbs::for('categories.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Категории', route('categories.index'));
    });

    Breadcrumbs::for('categories.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('categories.index');
        $breadcrumbs->push('Создание категории', route('categories.create'));
    });

    Breadcrumbs::for('categories.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Category $category) {
        $breadcrumbs->parent('categories.index');
        $breadcrumbs->push($category->getName(), route('categories.show', $category));
    });

    Breadcrumbs::for('categories.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Category $category) {
        $breadcrumbs->parent('categories.show', $category);
        $breadcrumbs->push('Редактирование', route('categories.edit', $category));
    });

    Breadcrumbs::for('social-networks.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Соц.сети', route('social-networks.index'));
    });

    Breadcrumbs::for('social-networks.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('social-networks.index');
        $breadcrumbs->push('Добавление соц.сети', route('social-networks.create'));
    });

    Breadcrumbs::for('social-networks.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, SocialNetwork $socialNetwork) {
        $breadcrumbs->parent('social-networks.index');
        $breadcrumbs->push($socialNetwork->getName(), route('social-networks.show', $socialNetwork));
    });

    Breadcrumbs::for('social-networks.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, SocialNetwork $socialNetwork) {
        $breadcrumbs->parent('social-networks.show', $socialNetwork);
        $breadcrumbs->push('Редактирование', route('social-networks.edit', $socialNetwork));
    });

    Breadcrumbs::for('tags.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Теги', route('tags.index'));
    });

    Breadcrumbs::for('tags.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('tags.index');
        $breadcrumbs->push('Создание тега', route('tags.create'));
    });

    Breadcrumbs::for('tags.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Tag $tag) {
        $breadcrumbs->parent('tags.index');
        $breadcrumbs->push($tag->getName(), route('tags.show', $tag));
    });

    Breadcrumbs::for('tags.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Tag $tag) {
        $breadcrumbs->parent('tags.show', $tag);
        $breadcrumbs->push('Редактирование', route('tags.edit', $tag));
    });

    Breadcrumbs::for('products.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('organizations.show', $organization);
        $breadcrumbs->push('Акции', route('products.index', $organization));
    });

    Breadcrumbs::for('products.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization) {
        $breadcrumbs->parent('products.index', $organization);
        $breadcrumbs->push('Создание акции', route('products.create', $organization));
    });

    Breadcrumbs::for('products.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization, Product $product) {
        $breadcrumbs->parent('products.index', $organization);
        $breadcrumbs->push($product->getName(), route('products.show', [$organization, $product]));
    });

    Breadcrumbs::for('products.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Organization $organization, Product $product) {
        $breadcrumbs->parent('products.show', $organization, $product);
        $breadcrumbs->push('Редактирование', route('products.edit', [$organization, $product]));
    });

    Breadcrumbs::for('auth-providers.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Сервисы авторизации', route('auth-providers.index'));
    });

    Breadcrumbs::for('auth-providers.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, \App\Models\Users\Auth\AuthProvider $provider) {
        $breadcrumbs->parent('auth-providers.index');
        $breadcrumbs->push($provider->getName(), route('auth-providers.edit', $provider));
    });

    Breadcrumbs::for('users.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Пользователи', route('users.index'));
    });

    Breadcrumbs::for('users.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('users.index');
        $breadcrumbs->push('Создание пользователя', route('users.create'));
    });

    Breadcrumbs::for('users.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.index');
        $breadcrumbs->push($user->getName(), route('users.edit', $user));
    });

    Breadcrumbs::for('users.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.show', $user);
        $breadcrumbs->push('Редактирование', route('users.edit', $user));
    });

    Breadcrumbs::for('users.roles', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.show', $user);
        $breadcrumbs->push('Роли', route('users.roles', $user));
    });

    Breadcrumbs::for('users.permissions', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.show', $user);
        $breadcrumbs->push('Разрешения', route('users.permissions', $user));
    });

    Breadcrumbs::for('users.organizations', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.show', $user);
        $breadcrumbs->push('Организации', route('users.organizations', $user));
    });

    Breadcrumbs::for('users.edit.password', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, User $user) {
        $breadcrumbs->parent('users.show', $user);
        $breadcrumbs->push('Смена пароля', route('users.edit.password', $user));
    });

    Breadcrumbs::for('roles.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Роли', route('roles.index'));
    });

    Breadcrumbs::for('roles.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Role $role) {
        $breadcrumbs->parent('roles.index');
        $breadcrumbs->push($role->getDisplayName(), route('roles.show', $role));
    });

    Breadcrumbs::for('roles.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Role $role) {
        $breadcrumbs->parent('roles.show', $role);
        $breadcrumbs->push('Редактирование', route('roles.edit', $role));
    });

    Breadcrumbs::for('roles.permissions', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Role $role) {
        $breadcrumbs->parent('roles.show', $role);
        $breadcrumbs->push('Разрешения', route('roles.permissions', $role));
    });

    Breadcrumbs::for('permissions.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Разрешения', route('permissions.index'));
    });

    Breadcrumbs::for('permissions.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Permission $permission) {
        $breadcrumbs->parent('permissions.index');
        $breadcrumbs->push($permission->getDisplayName(), route('permissions.show', $permission));
    });

    Breadcrumbs::for('permissions.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Permission $permission) {
        $breadcrumbs->parent('permissions.show', $permission);
        $breadcrumbs->push('Редактирование', route('permissions.edit', $permission));
    });

    Breadcrumbs::for('articles.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Блог', route('articles.index'));
    });

    Breadcrumbs::for('articles.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('articles.index');
        $breadcrumbs->push('Создание акции', route('articles.create'));
    });

    Breadcrumbs::for('articles.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Article $article) {
        $breadcrumbs->parent('articles.index', $article);
        $breadcrumbs->push($article->getName(), route('articles.show', $article));
    });

    Breadcrumbs::for('articles.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Article $article) {
        $breadcrumbs->parent('articles.show', $article);
        $breadcrumbs->push('Редактирование', route('articles.edit', $article));
    });

    Breadcrumbs::for('article-labels.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Лейблы для статей', route('article-labels.index'));
    });

    Breadcrumbs::for('article-labels.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('article-labels.index');
        $breadcrumbs->push('Создание лейбла', route('article-labels.create'));
    });

    Breadcrumbs::for('article-labels.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, ArticleLabel $articleLabel) {
        $breadcrumbs->parent('article-labels.index', $articleLabel);
        $breadcrumbs->push($articleLabel->getName(), route('article-labels.show', $articleLabel));
    });

    Breadcrumbs::for('article-labels.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, ArticleLabel $articleLabel) {
        $breadcrumbs->parent('article-labels.show', $articleLabel);
        $breadcrumbs->push('Редактирование', route('article-labels.edit', $articleLabel));
    });

    Breadcrumbs::for('auditories.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Аудитория', route('auditories.index'));
    });

    Breadcrumbs::for('auditories.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('auditories.index');
        $breadcrumbs->push('Создание аудитории', route('auditories.create'));
    });

    Breadcrumbs::for('auditories.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Auditory $auditory) {
        $breadcrumbs->parent('auditories.index');
        $breadcrumbs->push($auditory->getName(), route('auditories.show', $auditory));
    });

    Breadcrumbs::for('auditories.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Auditory $auditory) {
        $breadcrumbs->parent('auditories.show', $auditory);
        $breadcrumbs->push('Редактирование', route('auditories.edit', $auditory));
    });

    Breadcrumbs::for('holidays.index', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Праздники', route('holidays.index'));
    });

    Breadcrumbs::for('holidays.create', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('holidays.index');
        $breadcrumbs->push('Создание праздника', route('holidays.create'));
    });

    Breadcrumbs::for('holidays.show', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Holiday $holiday) {
        $breadcrumbs->parent('holidays.index');
        $breadcrumbs->push($holiday->getName(), route('holidays.show', $holiday));
    });

    Breadcrumbs::for('holidays.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs, Holiday $holiday) {
        $breadcrumbs->parent('holidays.show', $holiday);
        $breadcrumbs->push('Редактирование', route('holidays.edit', $holiday));
    });

    Breadcrumbs::for('cabinet.edit', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Кабинет', route('cabinet.edit'));
    });

    Breadcrumbs::for('cabinet.update.password.view', function (\DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator $breadcrumbs) {
        $breadcrumbs->parent('cabinet.edit');
        $breadcrumbs->push('Смена пароля', route('cabinet.update.password.view'));
    });
} catch (\DaveJamesMiller\Breadcrumbs\Exceptions\DuplicateBreadcrumbException $e) {

}

