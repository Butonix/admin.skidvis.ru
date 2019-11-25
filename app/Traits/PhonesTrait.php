<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 29.07.2019
 * Time: 12:45
 */

namespace App\Traits;

use App\Models\Communications\Phone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

/**
 * Trait EmailsTrait
 * @package App\Traits
 * @property-read \App\Models\Communications\Phone|null                                       $phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Phone[] $phones
 * @mixin \Eloquent
 */
trait PhonesTrait {
    /**
     * @return MorphMany
     */
    public function phones(): MorphMany {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    /**
     * @return Collection
     */
    public function getPhones(): Collection {
        return $this->phones;
    }

    /**
     * @return BelongsTo
     */
    public function phone(): BelongsTo {
        return $this->belongsTo(Phone::class);
    }

    /**
     * @return bool
     */
    public function hasPhone(): bool {
        return !is_null($this->phone);
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string {
        /**
         * @var Phone $phone
         */
        $phone = $this->phone;

        if (is_null($phone)) {
            return null;
        } else {
            return $phone->getFullPhone();
        }
    }

    /**
     * @param null|string $fullPhone
     *
     * @throws \Exception
     */
    public function updatePhone(?string $fullPhone): void {
        $phone = $this->phone;

        if (isset($fullPhone)) {
            /**
             * @var Phone $phone
             */
            if (!is_null($phone)) {
                $phone->update([
                    'full_phone' => $fullPhone,
                ]);
            } else {
                $phone = $this->phones()->save(new Phone([
                    'full_phone' => $fullPhone,
                ]));
                $this->phone()->associate($phone);
				$this->save();
            }

            try {
                $phoneNumber = explode(' ', $fullPhone);
                preg_match('/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/', $phoneNumber[1], $matches);
                $phoneWithoutCode = $matches[0];
                $phone->setCode(substr($phoneNumber[0], 1));
                $phone->setPhone($phoneWithoutCode);
                $phone->save();
            } catch (\Exception $exception) {
                Log::error('PhonesTrait@updatePhone parsing phone', [
                    'message' => $exception->getMessage(),
                    'line'    => $exception->getLine(),
                    'code'    => $exception->getCode()
                ]);
            }
        } else {
            if (!is_null($phone)) {
                $phone->delete();
                $this->phone()->dissociate();
            }
        }
    }
}
