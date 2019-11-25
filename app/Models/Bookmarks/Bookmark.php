<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 14:05
 */

namespace App\Models\Bookmarks;

use App\Models\Articles\Article;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Bookmarks\Bookmark
 *
 * @property int                                                                            $id
 * @property int|null                                                                       $user_id
 * @property string|null                                                                    $bookmarkable_id
 * @property string|null                                                                    $bookmarkable_type
 * @property \Illuminate\Support\Carbon|null                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[] $bookmarkable
 * @property-read \App\Models\Users\User|null                                               $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereBookmarkableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereBookmarkableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bookmarks\Bookmark whereUserId($value)
 * @mixin \Eloquent
 */
class Bookmark extends Model {
    protected $fillable = ['user_id', 'bookmarkable_id', 'bookmarkable_type'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * @return MorphTo
     */
    public function bookmarkable(): MorphTo {
        return $this->morphTo();
    }

    /**
     * @param array $articles
     * @param User  $user
     */
    public static function saveArticles(array $articles, User $user): void {
        foreach ($articles as $articleId) {
            $bookmarkArticle = $user->bookmarksArticles()
                                    ->whereHasMorph('bookmarkable', Article::class, function (Builder $query) use ($articleId) {
                                        $query->whereKey($articleId);
                                    });
            //Добавляем статью в избранное пользователю лишь в том случае,
            //если она еще не была добавлен
            if ($bookmarkArticle->doesntExist()) {
                /**
                 * @var Article  $article
                 * @var Bookmark $newBookmarkArticle
                 */
                $article = Article::whereKey($articleId)->first();

                $newBookmarkArticle = $user->bookmarksArticles()->save(new Bookmark());
                $newBookmarkArticle->bookmarkable()->associate($article);
                $newBookmarkArticle->save();
            }
        }
    }

    /**
     * @param array $products
     * @param User  $user
     */
    public static function saveProducts(array $products, User $user): void {
        foreach ($products as $productId) {
            $bookmarkProduct = $user->bookmarksProducts()
                                    ->whereHasMorph('bookmarkable', Product::class, function (Builder $query) use ($productId) {
                                        $query->whereKey($productId);
                                    });
            //Добавляем продукт в избранное пользователю лишь в том случае,
            //если продукт еще не был добавлен
            if ($bookmarkProduct->doesntExist()) {
                /**
                 * @var Product  $product
                 * @var Bookmark $newBookmarkProduct
                 */
                $product = Product::whereKey($productId)->first();

                $newBookmarkProduct = $user->bookmarksProducts()->save(new Bookmark());
                $newBookmarkProduct->bookmarkable()->associate($product);
                $newBookmarkProduct->save();

                $organization = $product->getOrganization();
                $organization->wishlist_count++;
                $organization->save();
            }
        }
    }
}
