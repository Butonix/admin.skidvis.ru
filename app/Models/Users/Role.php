<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laratrust\Models\LaratrustRole;

/**
 * App\Models\Users\Role
 *
 * @property int                                                                          $id
 * @property string                                                                       $name
 * @property string|null                                                                  $display_name
 * @property string|null                                                                  $description
 * @property \Illuminate\Support\Carbon|null                                              $created_at
 * @property \Illuminate\Support\Carbon|null                                              $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\User[]       $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Permission[] $permissions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role rolesWithout($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role usingRoles()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends LaratrustRole {
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * @return string
     */
    public function getName(): string {
        return $this->{'name'};
    }

    /**
     * @return null|string
     */
    public function getDisplayName(): ?string {
        return $this->{'display_name'};
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string {
        return $this->{'description'};
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUsingRoles(Builder $query): Builder {
        return $query->whereHas('users');
    }

    /**
     * @return array
     */
    public static function getRolesList(): array {
        return self::orderBy('display_name')->pluck('roles.display_name', 'roles.id')->toArray();
    }

    /**
     * @return array
     */
    public static function usingRolesList(): array {
        $result = [];
        $usingRoles = self::usingRoles()->orderBy('name', 'ASC');

        if ($usingRoles->get()->isNotEmpty() > 0) {
            return $usingRoles->pluck('display_name', 'id')->toArray();
        }

        return $result;
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
            $query->orWhere('name', 'like', '%' . $value . '%')
                  ->orWhere('display_name', 'like', '%' . $value . '%')
                  ->orWhere('description', 'like', '%' . $value . '%');
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
            if (null === $value)
                continue;

            switch ($key) {
                case 'search':
                    $query->search($value);
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeRolesWithout(Builder $query, array $ids): Builder {
        return $query->whereNotIn('id', $ids);
    }
}
