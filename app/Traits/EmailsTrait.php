<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 29.07.2019
 * Time: 15:37
 */

namespace App\Traits;


use App\Models\Communications\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait EmailsTrait
 * @package App\Traits
 * @property-read \App\Models\Communications\Email|null                                       $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Email[] $emails
 * @mixin \Eloquent
 */
trait EmailsTrait {
    /**
     * @return BelongsTo
     */
    public function email(): BelongsTo {
        return $this->belongsTo(Email::class);
    }

    /**
     * @return bool
     */
    public function hasEmail(): bool {
        return !is_null($this->email);
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string {
        /**
         * @var Email $email
         */
        $email = $this->email;

        if (is_null($email)) {
            return null;
        } else {
            return $email->getEmail();
        }
    }

    /**
     * @return MorphMany
     */
    public function emails(): MorphMany {
        return $this->morphMany(Email::class, 'emailable');
    }

    /**
     * @return Collection
     */
    public function getEmails(): Collection {
        return $this->emails;
    }

    /**
     * @param null|string $email
     * @return bool
     * @throws \Exception
     */
    public function updateEmail(?string $email): void {
        $oldEmail = $this->email;

        if (isset($email)) {
            if (!is_null($oldEmail)) {
                $oldEmail->update(['email' => $email]);
            } else {
                $newEmail = $this->emails()->save(new Email(['email' => $email]));
                $this->email()->associate($newEmail);
				$this->save();
            }
        } else {
            if (!is_null($oldEmail)) {
                $oldEmail->delete();
                $this->email()->dissociate();
				$this->save();
            }
        }
    }
}
