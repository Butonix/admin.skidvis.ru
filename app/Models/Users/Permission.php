<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\LaratrustPermission;

/**
 * App\Models\Users\Permission
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property string|null                                                            $display_name
 * @property string|null                                                            $description
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission permissionsWithout($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission usingPermissionsInRoles()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends LaratrustPermission {
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUsingPermissionsInRoles(Builder $query): Builder {
        return $query->whereHas('roles');
    }

    /**
     * @return array
     */
    public function usingPermissionsInRolesList(): array {
        $result = [];
        $usingPermissions = self::usingPermissionsInRoles()->orderBy('name', 'ASC');

        if ($usingPermissions->get()->isNotEmpty() > 0) {
            return $usingPermissions->pluck('display_name', 'id')->toArray();
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
     * @param string $name
     * @param string $displayName
     * @param string $description
     */
    public function createCrud(string $name, string $displayName, string $description = null) {
        $this->create([
            'name'         => str_slug($name) . '--create',
            'display_name' => $displayName . ' - создание',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--read',
            'display_name' => $displayName . ' - чтение',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--update',
            'display_name' => $displayName . ' - обновление',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--delete',
            'display_name' => $displayName . ' - удаление',
            'description'  => $description ?? '',
        ]);
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopePermissionsWithout(Builder $query, array $ids): Builder {
        return $query->whereNotIn('id', $ids);
    }
}
