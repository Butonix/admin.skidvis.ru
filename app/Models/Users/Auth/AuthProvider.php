<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 12.06.2019
 * Time: 17:50
 */

namespace App\Models\Users\Auth;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\AuthProviders\AuthProvider
 *
 * @property int                                                                                $id
 * @property string                                                                             $name
 * @property string|null                                                                        $slug
 * @property string|null                                                                        $icon_url
 * @property int|null                                                                           $ordering
 * @property int                                                                                $published
 * @property \Illuminate\Support\Carbon|null                                                    $created_at
 * @property \Illuminate\Support\Carbon|null                                                    $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\Auth\UserAccount[] $accounts
 * @property array|null                                                                         $payload
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\AuthProvider published()
 * @mixin \Eloquent
 */
class AuthProvider extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'icon_url', 'ordering', 'published', 'payload'];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
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
    public function getSlug(): ?string {
        return $this->{'slug'};
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName() {
        return 'slug';
    }

    /**
     * @return null|string
     */
    public function getIconUrl(): ?string {
        return $this->{'icon_url'};
    }

    /**
     * @return int|null
     */
    public function getOrdering(): ?int {
        return $this->{'ordering'};
    }

    /**
     * @return bool
     */
    public function isPublished(): bool {
        return $this->{'published'};
    }

    /**
     *
     */
    public function unsetPublished(): void {
        $this->{'published'} = false;
    }

    /**
     *
     */
    public function setPublished(): void {
        $this->{'published'} = true;
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder {
        return $query->where('published', true);
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        foreach ($frd as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            switch ($key) {

            }
        }

        return $query;
    }

    /**
     * @return array
     */
    public function getPayload(): ?array {
        return $this->{'payload'};
    }

    /**
     * @param $key
     * @param $value
     */
    public function setPayload($key, $value) {
        $payload = $this->getPayload();
        $payload[$key] = $value;
        $this->{'payload'} = $payload;
    }

    /**
     * @return string
     */
    public function getClientIdConfig(): ?string {
        return $this->getPayload()['client_id']['config_value'];
    }

    /**
     * @return string
     */
    public function getClientSecretConfig(): ?string {
        return $this->getPayload()['client_secret']['config_value'];
    }

    /**
     * @return string
     */
    public function getRedirectConfig(): ?string {
        return $this->getPayload()['redirect']['config_value'];
    }

    /**
     * @return array
     */
    public static function getConfigs(): array {
        $authProviders = self::get();
        $result = [];

        foreach ($authProviders as $authProvider) {
            $payload = $authProvider->getPayload();
            $clientId = $payload['client_id'];
            $clientSecret = $payload['client_secret'];
            $redirect = $payload['redirect'];
            $result[$clientId['config_key']] = $clientId['config_value'];
            $result[$clientSecret['config_key']] = $clientSecret['config_value'];
            $result[$redirect['config_key']] = $redirect['config_value'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getConfigsForApi(): array {
        $authProviders = self::get();
        $result = [];

        foreach ($authProviders as $authProvider) {
            $payload = $authProvider->getPayload();
            $clientId = $payload['client_id'];
            $clientSecret = $payload['client_secret'];
            $redirect = $payload['redirect'];
            $redirect['config_value'] = parse_url($redirect['config_value']);
            $redirect['config_value'] = $redirect['config_value']['scheme'] . '://' . $redirect['config_value']['host'] . '/api' . $redirect['config_value']['path'];
            //dd($redirect);
            $result[$clientId['config_key']] = $clientId['config_value'];
            $result[$clientSecret['config_key']] = $clientSecret['config_value'];
            $result[$redirect['config_key']] = $redirect['config_value'];
        }

        return $result;
    }
}
