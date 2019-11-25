<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 31.07.2019
 * Time: 14:50
 */

namespace App\Traits;


use App\Models\Social\SocialAccount;
use App\Models\Social\SocialNetwork;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait SocialsTrait
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[] $socialAccounts
 * @package App\Traits
 * @mixin \Eloquent
 */
trait SocialsTrait {
    /**
     * @return MorphMany
     */
    public function socialAccounts(): MorphMany {
        return $this->morphMany(SocialAccount::class, 'social_account');
    }

    /**
     * @return Collection
     */
    public function getSocialAccounts(): Collection {
        return $this->socialAccounts;
    }

    /**
     * @return array
     */
    public function getSocialLinks(): array {
        $accounts = $this->getSocialAccounts();
        $result = [];

        foreach ($accounts as $account) {
            /**
             * @var SocialAccount $account
             */
            $result[] = [
                'id'   => $account->getKey(),
                'link' => $account->getLink(),
                'type' => $account->getSocialNetworkName(),
            ];
        }

        return $result;
    }

    /**
     * @param array $socialAccounts
     *
     * @throws \Exception
     */
    public function updateSocialAccounts(array $socialAccounts) {
        if (!empty($socialAccounts)) {
            $socialNetworks = SocialNetwork::all()->keyBy('name')->toArray(); //Массив с ключами всех соц.сетей

            //Массив с id всех добавленных ссылок на соц.сети.
            $oldSocialAccounts = $this->getSocialAccounts()->keyBy('id');
            $oldSocialAccountsKeys = $oldSocialAccounts->keys()->toArray();
            $newSocialAccounts = []; //Массив для хранения всех ссылок на соц.сети, что пришли на сохранение
            //$newSocialAccountsWithNetworkType = []; //Массив для хранения типов всех ссылок на соц.сети, что пришли на сохранение

            //Данным циклом сохраняем по массивам все данные о соц.сетях и их ссылках, что пришли на сохранение
            foreach ($socialAccounts as $socialAccount) {
                $socialAccountId = $socialAccount['id'] ?? null;
                $socialAccountType = $socialAccount['type'];
                $socialAccountLink = $socialAccount['link'];
                $newSocialAccounts[] = $socialAccountId;

                if (isset($socialAccountId)) {
                    /**
                     * @var SocialAccount $socAcc
                     */
                    $socAcc = $oldSocialAccounts[$socialAccountId];
                    $socAcc->update([
                        'type' => $socialAccountType,
                        'link' => $socialAccountLink
                    ]);
                } else {
                    //$newSocialAccountsWithNetworkType[$socialNetworkLink] = $socialNetworkType;
                    //if (!isset($oldSocialAccounts[$socialNetworkLink])) { //Сохраняем новый соц.аккаунт при его отсутствии
                        $socialAccount = $this->socialAccounts()->save(new SocialAccount([
                            'link'              => $socialAccountLink,
                            'social_network_id' => $socialNetworks[$socialAccountType]['id'] ?? null
                        ]));
                    //}
                }
            }

            //Сравнение массивов для выяснения, какие ссылки нужно удалить
            $socialAccountsForDelete = array_diff($oldSocialAccountsKeys, $newSocialAccounts);
            foreach ($socialAccountsForDelete as $socialAccountId) {
                /**
                 * @var SocialAccount $socialAccountForDelete
                 */
                $socialAccountForDelete = $oldSocialAccounts[$socialAccountId];
                $socialAccountForDelete->delete();
            }
        } else {
            $oldSocialAccounts = $this->getSocialAccounts();

            foreach ($oldSocialAccounts as $oldSocialAccount) {
                $oldSocialAccount->delete();
            }
        }
    }
}
