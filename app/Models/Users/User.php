<?php

namespace App\Models\Users;

use App\Http\Resources\Articles\ArticleCollection;
use App\Http\Resources\Products\ProductCollection;
use App\Models\Articles\Article;
use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Products\Product;
use App\Models\Reviews\Like;
use App\Models\Reviews\Rating;
use App\Models\Reviews\Review;
use App\Models\Social\SocialAccount;
use App\Models\Cities\City;
use App\Models\Users\Auth\UserAccount;
use App\Notifications\ResetPassword;
use App\Traits\EmailsTrait;
use App\Traits\ImagesTrait;
use App\Traits\PhonesTrait;
use App\Traits\SocialsTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\Users\User
 *
 * @property int                                                                                                            $id
 * @property string|null                                                                                                    $f_name
 * @property string|null                                                                                                    $l_name
 * @property string|null                                                                                                    $m_name
 * @property string                                                                                                         $email
 * @property string|null                                                                                                    $phone
 * @property \Illuminate\Support\Carbon|null                                                                                $email_verified_at
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $remember_token
 * @property int|null                                                                                                       $avatar_id
 * @property \Illuminate\Support\Carbon|null                                                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                                                $updated_at
 * @property string|null                                                                                                    $deleted_at
 * @property int|null                                                                                                       $city_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Permission[]                                   $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Role[]                                         $roles
 * @property-read \App\Models\Files\Image                                                                                   $cover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Rating[]                                     $ratings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Review[]                                     $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]                                        $sliderImages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[]                                 $bookmarksArticles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[]                                 $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[]                                 $bookmarksProducts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\Organization[]                         $createdOrganizations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\Organization[]                         $organizations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[]                                   $products
 * @property-read \App\Models\Files\Image|null                                                                              $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Like[]                                       $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[]                                   $productsWishlist
 * @property-read \App\Models\Cities\City|null                                                                              $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Email[]                               $emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]                                        $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Phone[]                               $phones
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[]                               $socialAccounts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Auth\UserAccount[]                             $accounts
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User orWherePermissionIs($permission = '')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User orWhereRoleIs($role = '', $team = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereFName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereLName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereMName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User wherePermissionIs($permission = '', $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRoleIs($role = '', $team = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereAvatarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User notifiableUsers()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\User onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\User withoutTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail, CanResetPassword {
    use LaratrustUserTrait;
    use Notifiable;
    use SoftDeletes;
    use PhonesTrait;
    use ImagesTrait;
    use EmailsTrait;
    use SocialsTrait;

    //Использование данного параметра означает,
    //что все акции из избранного отдаются в виде объектов акций с пагинацией
    const WISHLIST_TYPE_PRODUCTS = 1;

    //Использование данного параметра означает,
    //что все акции из избранного отдаются в виде массива с их id
    const WISHLIST_TYPE_PRODUCTS_IDS = 2;

    //Использование данного параметра означает,
    //что все статьи из избранного отдаются в виде объектов акций с пагинацией
    const BOOKMARKS_ARTICLES = 1;

    //Использование данного параметра означает,
    //что все статьи из избранного отдаются в виде массива с их id
    const BOOKMARKS_ARTICLES_IDS = 2;

    /**
     * @var string
     */
    protected $superAdministratorRole = 'super_administrator';

    /**
     * @var string
     */
    protected $administratorRole = 'administrator';

    /**
     * @var array
     */
    protected $adminsRoles = ['super_administrator', 'administrator'];

    /**
     * @var array
     */
    protected $managementRoles = ['management'];

    /**
     * @var array
     */
    protected $moderatorRoles = ['moderator'];

    /**
     * @var array
     */
    protected $notifiableRoles = ['super_administrator'];

    /**
     * @var array
     */
    protected $notifiableRolesWithout = [11, 14, 18];

    /**
     * @var array
     */
    protected $rolesWhichCanEditItself = ['super_administrator'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name',
        'l_name',
        'm_name',
        'email',
        'password',
        'phone',
        'avatar_id',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var array
     */
    protected static $rules = [
        'f_name'   => ['required', 'string', 'max:255'],
        'l_name'   => ['nullable', 'string', 'max:255'],
        'm_name'   => ['nullable', 'string', 'max:255'],
        'phone'    => ['nullable', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'f_name.required'   => 'Укажите ваше имя',
        'email.required'    => 'Укажите ваш Email',
        'password.required' => 'Укажите ваш пароль',
        'password.min'      => 'Длина пароля не менее :min символов',
    ];

    /**
     * @return array
     */
    public static function getRules(): array {
        return self::$rules;
    }

    /**
     * @return array
     */
    public static function getMessages(): array {
        return self::$messages;
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * @return null|string
     */
    public function getFirstName(): ?string {
        return mb_convert_case($this->{'f_name'}, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string {
        return mb_convert_case($this->{'l_name'}, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @return null|string
     */
    public function getMiddleName(): ?string {
        return mb_convert_case($this->{'m_name'}, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getName(): string {
        return trim($this->getLastName() . ' ' . $this->getFirstName() . ' ' . $this->getMiddleName());
    }

    /**
     * @return string
     */
    public function getShortName(): string {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();
        $result = null;

        (!isset($firstName))
            ?: $result .= $firstName;
        (!isset($lastName))
            ?: $result .= ' ' . mb_strtoupper(mb_substr($lastName, 0, 1)) . '.'; //Если есть фамилия, то добавляем первую букву к инициалам, делая её заглавной

        return $result;
    }

    /**
     * @return null|string
     */
    public function getInitials(): ?string {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();
        $result = null;

        (!isset($firstName))
            ?: $result .= mb_strtoupper(mb_substr($firstName, 0, 1)); //Если есть имя, то добавляем первую букву к инициалам, делая её заглавной
        (!isset($lastName))
            ?: $result .= mb_strtoupper(mb_substr($lastName, 0, 1)); //Если есть фамилия, то добавляем первую букву к инициалам, делая её заглавной

        return $result;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string {
        return $this->{'phone'};
    }

    /**
     * @return Collection
     */
    public function getPermissions(): Collection {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getPermissionsCount(): int {
        return count($this->getPermissionsIds());
    }

    /**
     * @return array
     */
    public function getPermissionsIds(): array {
        $permissionsIds = $this->permissions->keyBy('id')->keys()->toArray();
        $roles = $this->getRolesCollection();
        $rolesPermissionsIds = [];

        foreach ($roles as $role) {
            /**
             * @var Role $role
             */
            $permissions = $role->permissions->keyBy('id')->keys()->toArray();
            $newPermissions = array_diff($permissions, $rolesPermissionsIds);
            $rolesPermissionsIds = array_merge($rolesPermissionsIds, $newPermissions);
        }

        return array_merge($permissionsIds, $rolesPermissionsIds);
    }

    /**
     * Return all the user permissions.
     *
     * @return boolean
     */
    public function allPermissionsOfRoles() {
        $roles = $this->roles()->with('permissions')->get();

        $roles = $roles->flatMap(function ($role) {
            return $role->permissions;
        });

        return $roles;
    }

    /**
     * @return Collection
     */
    public function getRolesCollection(): Collection {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getRolesIds(): array {
        return $this->getRolesCollection()->keyBy('id')->keys()->toArray();
    }


    /**
     * @return array
     */
    public function getRoles(): array {
        $roles = $this->roles;
        $result = [];

        foreach ($roles as $role) {
            $result[] = [
                'id'           => $role->getKey(),
                'name'         => $role->getName(),
                'display_name' => $role->getDisplayName(),
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->{'email'};
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder {
        return $query->where(function ($query) use ($value) {
            /**
             * @var Builder $query
             */
            $query->orWhere('id', $value)
                  ->orWhere('f_name', 'like', '%' . $value . '%')
                  ->orWhere('l_name', 'like', '%' . $value . '%')
                  ->orWhere('m_name', 'like', '%' . $value . '%')
                  ->orWhere('phone', 'like', '%' . $value . '%')
                  ->orWhere('email', 'like', '%' . $value . '%');
        });
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        foreach ($frd as $key => $value) {
            if (null === $value) {
                continue;
            }

            switch ($key) {
                case 'search':
                    $query->search($value);
                    break;
                case 'role_id':
                    $query->whereHas('roles', function (Builder $query) use ($value) {
                        $query->where('id', $value);
                    });
                    break;
                case 'permission_id':
                    $query->whereHas('roles', function (Builder $query) use ($value) {
                        $query->whereHas('permissions', function (Builder $query) use ($value) {
                            $query->where('id', $value);
                        });
                    });
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * @return null|string
     */
    public function getSuperAdministratorRole(): ?string {
        return $this->superAdministratorRole;
    }

    /**
     * @return array
     */
    public function getAdminsRoles(): array {
        return $this->adminsRoles;
    }

    /**
     * @return array
     */
    public function getNotifiableRoles(): array {
        return $this->notifiableRoles;
    }

    /**
     * @return array
     */
    public function getNotifiableRolesWithout(): array {
        return $this->notifiableRolesWithout;
    }

    /**
     * @return null|string
     */
    public function getAdministratorRole(): ?string {
        return $this->administratorRole;
    }

    /**
     * @return array
     */
    public function getManagementRoles(): array {
        return $this->managementRoles;
    }

    /**
     * @return array
     */
    public function getModeratorRoles(): array {
        return $this->moderatorRoles;
    }

    /**
     * @return array
     */
    public function getRolesWhichCanEditItself(): array {
        return $this->rolesWhichCanEditItself;
    }

    /**
     * @param $roles
     *
     * @return bool
     */
    public function catAttachRole($roles): bool {
        if ($this->hasRole('super_administrator')) {
            return true;
        }

        if (($this->hasRole([
                    'technical_administrator',
                    'administrator',
                ]) && in_array('super_administrator', $roles)) || ($this->hasRole('administrator') && in_array('technical_administrator', $roles))) {
            return false;
        }

        foreach ($this->getAdminsRoles() as $role) {
            if ($role === 'administrator' && in_array($role, $roles)) {
                continue;
            }

            if ($this->hasRole($role) && in_array($role, $roles)) {
                return false;
            }
        }

        if ($this->hasRole($this->getAdminsRoles())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $roles
     *
     * @return bool
     */
    public function canEditUsersRoles($roles): bool {
        if ($this->hasRole('super_administrator')) {
            return true;
        }

        if (($this->hasRole([
                    'technical_administrator',
                    'administrator',
                ]) && in_array('super_administrator', $roles)) || ($this->hasRole('administrator') && in_array('technical_administrator', $roles))) {
            return false;
        }

        foreach ($this->getAdminsRoles() as $role) {
            if ($this->hasRole($role) && in_array($role, $roles)) {
                return false;
            }
        }

        if ($this->hasRole($this->getAdminsRoles())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function canEditOwnRoles(): bool {
        $rolesEditItself = $this->getRolesWhichCanEditItself();
        if (empty($rolesEditItself)) {
            return false;
        } else {
            return $this->hasRole($rolesEditItself);
        }
    }

    /**
     * @return bool
     */
    public function isSuperAdministrator(): bool {
        return $this->hasRole($this->getSuperAdministratorRole());
    }

    /**
     * @return bool
     */
    public function isAdministrator(): bool {
        return $this->hasRole($this->getAdministratorRole());
    }

    /**
     * @return bool
     */
    public function isNotAdministrator(): bool {
        return !$this->hasRole($this->getAdministratorRole());
    }

    /**
     * @return bool
     */
    public function isModerator(): bool {
        return $this->hasRole($this->getModeratorRoles());
    }

    /**
     * @return bool
     */
    public function canWorkWithArticles(): bool {
        return $this->can(['articles--read', 'articles--create', 'articles--delete', 'articles--update']);
    }

    /**
     * @return bool
     */
    public function canCreateArticle(): bool {
        return $this->can('articles--create');
    }

    /**
     * @return bool
     */
    public function canDeleteArticle(): bool {
        return $this->can('articles--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateArticle(): bool {
        return $this->can('articles--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithCategories(): bool {
        return $this->can(['categories--read', 'categories--create', 'categories--delete', 'categories--update']);
    }

    /**
     * @return bool
     */
    public function canCreateCategories(): bool {
        return $this->can('categories--create');
    }

    /**
     * @return bool
     */
    public function canDeleteCategories(): bool {
        return $this->can('categories--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateCategories(): bool {
        return $this->can('categories--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithTags(): bool {
        return $this->can(['tags--read', 'tags--create', 'tags--delete', 'tags--update']);
    }

    /**
     * @return bool
     */
    public function canCreateTags(): bool {
        return $this->can('tags--create');
    }

    /**
     * @return bool
     */
    public function canDeleteTags(): bool {
        return $this->can('tags--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateTags(): bool {
        return $this->can('tags--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithAuditories(): bool {
        return $this->can(['auditories--read', 'auditories--create', 'auditories--delete', 'auditories--update']);
    }

    /**
     * @return bool
     */
    public function canCreateAuditories(): bool {
        return $this->can('auditories--create');
    }

    /**
     * @return bool
     */
    public function canDeleteAuditories(): bool {
        return $this->can('auditories--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateAuditories(): bool {
        return $this->can('auditories--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithHolidays(): bool {
        return $this->can(['holidays--read', 'holidays--create', 'holidays--delete', 'holidays--update']);
    }

    /**
     * @return bool
     */
    public function canCreateHolidays(): bool {
        return $this->can('holidays--create');
    }

    /**
     * @return bool
     */
    public function canDeleteHolidays(): bool {
        return $this->can('holidays--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateHolidays(): bool {
        return $this->can('holidays--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithOrganizations(): bool {
        return $this->can([
            'organizations--read',
            'organizations--create',
            'organizations--delete',
            'organizations--update',
        ]);
    }

    /**
     * @return bool
     */
    public function canCreateOrganizations(): bool {
        return $this->can('organizations--create');
    }

    /**
     * @return bool
     */
    public function canDeleteOrganizations(): bool {
        return $this->can('organizations--delete');
    }

    /**
     * @return bool
     */
    public function canUpdateOrganizations(): bool {
        return $this->can('organizations--update');
    }

    /**
     * @return bool
     */
    public function canWorkWithPoints(): bool {
        return $this->can(['points--read', 'points--create', 'points--delete', 'points--update']);
    }

    /**
     * @return bool
     */
    public function canCreatePoints(): bool {
        return $this->can('points--create');
    }

    /**
     * @return bool
     */
    public function canDeletePoints(): bool {
        return $this->can('points--delete');
    }

    /**
     * @return bool
     */
    public function canUpdatePoints(): bool {
        return $this->can('points--update');
    }

    /**
     * @return bool
     */
    public function isManager(): bool {
        return $this->hasRole($this->getManagementRoles());
    }

    /**
     * @return bool
     */
    public function isNotManager(): bool {
        return !$this->hasRole($this->getManagementRoles());
    }

    /**
     * @param $name
     */
    public function setFullNameSafely($name): void {
        $name = mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8');
        $nameArray = explode(' ', $name);
        if (\is_array($nameArray) && \count($nameArray) > 0) {
            $fName = $nameArray[0] ?? null;
            $this->setFirstName($fName);
            if (isset($nameArray[1], $nameArray[0])) {
                unset($nameArray[0]);
                $lName = implode($nameArray, ' ');
                $this->setLastName($lName);
            }
        }
    }

    /**
     * @param      $firstName
     * @param bool $force
     */
    public function setFirstName($firstName, bool $force = false): void {
        $fName = trim($this->getFirstName());
        if ($fName === '' || $force === true) {
            $this->{'f_name'} = mb_convert_case($firstName, MB_CASE_TITLE, 'UTF-8');
        }
    }

    /**
     * @param      $lastName
     * @param bool $force
     */
    public function setLastName($lastName, bool $force = false): void {
        $fName = trim($this->getLastName());
        if ($fName === '' || $force === true) {
            $this->{'l_name'} = mb_convert_case($lastName, MB_CASE_TITLE, 'UTF-8');
        }
    }

    /**
     * @return HasMany
     */
    public function accounts(): HasMany {
        return $this->hasMany(UserAccount::class);
    }

    /**
     * @return Collection
     */
    public function getAccounts(): Collection {
        return $this->accounts;
    }

    /**
     * @return bool
     */
    public function twoFactorAuthenticationEnabled(): bool {
        $result = false;

        if ($this->google2fa_enabled === 1) {
            $result = true;
        }

        return $result;
    }

    /**
     * @return mixed|string
     */
    public function getVirtualUserId() {
        if (session()->has('virtual_user_id')) {
            $virtualUserId = session()->get('virtual_user_id');
        } else {
            $virtualUserId = $this->generateVirtualUserId();
            $this->setVirtualUserId($virtualUserId);
        }

        return $virtualUserId;
    }

    /**
     * @return string
     */
    public function generateVirtualUserId(): string {
        $ip = request()->ip();
        $virtualUserId = $ip . '-' . time();

        return $virtualUserId;
    }

    /**
     * @param string $virtualUserId
     */
    public function setVirtualUserId(string $virtualUserId) {
        session()->put('virtual_user_id', $virtualUserId);
    }

    /**
     * @param string      $token
     * @param bool        $isApi
     * @param string|null $domain
     */
    public function sendPasswordResetNotification($token, bool $isApi = false, string $domain = null) {
        $this->notify(new ResetPassword($token, $isApi, $domain));
    }

    /**
     * @return HasMany
     */
    public function createdOrganizations(): HasMany {
        return $this->hasMany(Organization::class, 'creator_id');
    }

    /**
     * @return Collection
     */
    public function getCreatedOrganization(): Collection {
        return $this->createdOrganizations;
    }

    /**
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany {
        return $this->belongsToMany(Organization::class, 'organization_user', 'user_id', 'organization_id');
    }

    /**
     * @return Collection
     */
    public function getOrganizations(): Collection {
        return $this->organizations;
    }

    /**
     * @return int
     */
    public function getOrganizationsCount(): int {
        return $this->organizations->count();
    }

    /**
     * @return array
     */
    public function getOrganizationsIds(): array {
        return $this->getOrganizations()->keyBy('id')->keys()->toArray();
    }

    /**
     * @param int $organizationId
     *
     * @return bool
     */
    public function hasOrganization(int $organizationId): bool {
        return $this->organizations()->whereKey($organizationId)->exists();
    }

    /**
     * @return int
     */
    public function getRolesCount(): int {
        return $this->roles->count();
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }

    /**
     * @return Collection
     */
    public function getReviews(): Collection {
        return $this->reviews;
    }

    /**
     * @return HasMany
     */
    public function ratings(): HasMany {
        return $this->hasMany(Rating::class);
    }

    /**
     * @return Collection
     */
    public function getRatings(): Collection {
        return $this->ratings;
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    /**
     * @return BelongsTo
     */
    public function avatar(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getAvatar(): ?Image {
        return $this->avatar;
    }

    /**
     * @return null|string
     */
    public function getAvatarLink(): ?string {
        $avatar = $this->getAvatar();

        if (is_null($avatar)) {
            return null;
        }

        return $avatar->getPublishPath();
    }

    /**
     * @return int|null
     */
    public function getAvatarId(): ?int {
        $avatar = $this->getAvatar();

        if (is_null($avatar)) {
            return null;
        }

        return $avatar->getKey();
    }

    /**
     * @return BelongsToMany
     */
    public function productsWishlist(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'product_user_wishlist', 'user_id', 'product_id');
    }

    /**
     * @return Collection
     */
    public function getProductsWishlist(): Collection {
        return $this->productsWishlist;
    }

    /**
     * @return array
     */
    public function getProductsIdsWishlist(): array {
        $products = $this->getProductsWishlist()->keyBy('id');

        foreach ($products as $product) {
            /**
             * @var Product $product
             */
            if ($product->isUnpublished()) {
                unset($products[$product->getKey()]);
            }
        }

        return $products->keys()->toArray();
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany {
        return $this->hasMany(Like::class);
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection {
        return $this->likes;
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City {
        return $this->city;
    }

    /**
     * @return int|null
     */
    public function getCityId(): ?int {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getKey();
    }

    /**
     * @return null|string
     */
    public function getCityName(): ?string {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getName();
    }

    /**
     * @return null|string
     */
    public function getCityLatitude(): ?float {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getLatitude();
    }

    /**
     * @return null|string
     */
    public function getCityLongitude(): ?float {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getLongitude();
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotifiableUsers(Builder $query): Builder {
        return $query->whereHas('roles', function (Builder $query) {
            $query->where('name', $this->getNotifiableRoles());
        })->whereNotIn('id', $this->getNotifiableRolesWithout());
    }

    /**
     * @return DatabaseNotificationCollection|null
     */
    public function getNotifications(): ?DatabaseNotificationCollection {
        return $this->notifications;
    }

    /**
     * @return int
     */
    public function getUnreadNotificationsCount(): int {
        return $this->unreadNotifications()->count();
    }

    /**
     * @return HasMany
     */
    public function bookmarks(): HasMany {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * @return Collection
     */
    public function getBookmarks(): Collection {
        return $this->bookmarks;
    }

    /**
     * @return HasMany
     */
    public function bookmarksArticles(): HasMany {
        return $this->hasMany(Bookmark::class)->where('bookmarkable_type', Article::class);
    }

    /**
     * @return Collection
     */
    public function getBookmarksArticles(): Collection {
        return $this->bookmarksArticles;
    }

    /**
     * @return array
     */
    public function getBookmarksArticlesIds(): array {
        return $this->getBookmarksArticles()->keyBy('bookmarkable_id')->keys()->toArray();
    }

    /**
     * @return HasMany
     */
    public function bookmarksProducts(): HasMany {
        return $this->hasMany(Bookmark::class)->where('bookmarkable_type', Product::class);
    }

    /**
     * @return Collection
     */
    public function getBookmarksProducts(): Collection {
        return $this->bookmarksProducts;
    }

    /**
     * @return array
     */
    public function getBookmarksProductsIds(): array {
        $bookmarksProducts = $this->getBookmarksProducts()->keyBy('bookmarkable_id');

        foreach ($bookmarksProducts as $productId => $bookmark) {
            /**
             * @var Product $product
             */
            $product = Product::whereKey($productId)->first();

            if (is_null($product)) {
                unset($bookmarksProducts[$productId]);
                continue;
            }

            if ($product->isUnpublished()) {
                unset($bookmarksProducts[$product->getKey()]);
            }
        }

        return $bookmarksProducts->keys()->toArray();
    }

    /**
     * Метод для получения акций из избранного
     *
     * @param array $frd
     *
     * @return array
     */
    public function getBookmarksProductsItems(array $frd): array {
        $response = [];
        $responseTypeId = (isset($frd['responseTypeId']))
            ? (int)$frd['responseTypeId']
            : User::WISHLIST_TYPE_PRODUCTS;

        if ($responseTypeId === User::WISHLIST_TYPE_PRODUCTS) {
            $productsIds = $this->getBookmarksProductsIds();
            $products = Product::productsWhereIn($productsIds)
                               ->filter($frd)
                               ->ordering($frd)
                               ->paginate($frd['perPage'] ?? (new Product)->getPerPage());
            $response['list'] = (new ProductCollection($products))->additional([
                'meta' => [
                    'typeOfPublish' => Product::PRODUCT_PUBLISH_WITH_ORGANIZATION,
                ],
            ]);
        } elseif ($responseTypeId === User::WISHLIST_TYPE_PRODUCTS_IDS) {
            $response['list'] = $this->getBookmarksProductsIds();
        }

        return $response;
    }

    /**
     * Метод для получения статей из избранного
     *
     * @param array $frd
     *
     * @return array
     */
    public function getBookmarksArticlesItems(array $frd): array {
        $response = [];
        $responseTypeId = (isset($frd['responseTypeId']))
            ? (int)$frd['responseTypeId']
            : User::BOOKMARKS_ARTICLES;

        if ($responseTypeId === User::BOOKMARKS_ARTICLES) {
            $articlesIds = $this->getBookmarksArticlesIds();
            $articles = Article::articlesWhereIn($articlesIds)
                               ->filter($frd)
                               ->ordering($frd)
                               ->paginate($frd['perPage'] ?? (new Article)->getPerPage());
            $response['list'] = new ArticleCollection($articles);
        } elseif ($responseTypeId === User::BOOKMARKS_ARTICLES_IDS) {
            $response['list'] = $this->getBookmarksArticlesIds();
        }

        return $response;
    }

    /**
     * @param Organization $organization
     *
     * @return bool
     */
    public function hasNoAccess(Organization $organization): bool {
        $userOrganizations = Organization::organizationsByUser($this->getKey());

        return (($this->isAdministrator() || $this->isManager()) && $userOrganizations->whereKey($organization)
                                                                                      ->doesntExist());
    }

    /**
     * @param array $avatar
     *
     * @throws \Exception
     */
    public function updateAvatar(array $avatar): void {
        $oldAvatar = $this->getAvatar();

        if (!empty($avatar)) {
            if (isset($avatar['id'])) {
                $newAvatarId = $avatar['id'];

                if (!is_null($oldAvatar) && $oldAvatar->getKey() !== $newAvatarId) {
                    $this->deleteAvatar($oldAvatar);
                    /**
                     * @var Image $newAvatar
                     */
                    $newAvatar = Image::whereKey($newAvatarId)->first();

                    if (!is_null($newAvatar)) { //Сохраняем только если аватар найден
                        $this->saveAvatar($newAvatar);
                    }
                } elseif (is_null($oldAvatar)) {
                    /**
                     * @var Image $newAvatar
                     */
                    $newAvatar = Image::whereKey($newAvatarId)->first();

                    if (!is_null($newAvatar)) { //Сохраняем только если аватар найден
                        $this->saveAvatar($newAvatar);
                    }
                }
            } else {
                if (!is_null($oldAvatar)) {
                    $this->deleteAvatar($oldAvatar);
                }
            }
        } else {
            if (!is_null($oldAvatar)) {
                $this->deleteAvatar($oldAvatar);
            }
        }
    }

    /**
     * @param Image $newAvatar
     */
    public function saveAvatar(Image $newAvatar): void {
        $newAvatar = $this->images()->save($newAvatar);
        $this->avatar()->associate($newAvatar);
    }

    /**
     * @param Image $oldAvatar
     *
     * @throws \Exception
     */
    public function deleteAvatar(Image $oldAvatar): void {
        $oldAvatar->delete();
        $this->avatar()->dissociate();
    }
}
