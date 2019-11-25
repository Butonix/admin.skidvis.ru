<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 29.07.2019
 * Time: 15:57
 */

namespace App\Traits;

use App\Models\Articles\Article;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Products\Category;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;


/**
 * Trait ImagesTrait
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[] $sliderImages
 * @property-read \App\Models\Files\Image|null                                       $cover
 * @package App\Traits
 * @mixin \Eloquent
 */
trait ImagesTrait {
    /**
     * @return MorphMany
     */
    public function images(): MorphMany {
        return $this->morphMany(Image::class, 'fileable');
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection {
        return $this->images;
    }

    /**
     * @return MorphMany
     */
    public function sliderImages(): MorphMany {
        return $this->morphMany(Image::class, 'fileable')
                    ->where('public_path', 'like', '%' . 'slider_image' . '%')
                    ->where('file_parent_id', null);
    }

    ///**
    // * @return MorphMany
    // */
    //public function getSliderImagesCollection(): MorphMany {
    //    return $this->images()->filter(['public_path' => 'slider_image', 'file_parent_id' => 'null']);
    //}


    /**
     * @return Collection
     */
    public function getSliderImages(): Collection {
        return $this->sliderImages;
    }

    /**
     * @return BelongsTo
     */
    public function cover(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getCover(): ?Image {
        return $this->cover;
    }

    /**
     * @return int|null
     */
    public function getCoverId(): ?int {
        $cover = $this->getCover();

        if (is_null($cover)) {
            return null;
        }

        return $cover->getKey();
    }

    /**
     * @return null|string
     */
    public function getCoverLink(): ?string {
        $cover = $this->getCover();

        if (is_null($cover)) {
            return null;
        }

        return $cover->getPublishPath();
    }

    /**
     * @param int|null $imageId
     */
    public function updateCover(?int $imageId): void {
        if (isset($imageId)) {
            $newCover = Image::whereKey($imageId)->first();
            if (!is_null($newCover)) {
                $this->images()->save($newCover);
                $this->cover()->associate($newCover); //Сохраняем только если изображение найдено
            }
        } else {
            $this->cover()->dissociate();
        }
    }

    /**
     * @param array $images
     *
     * @throws \Exception
     */
    public function updateSliderImages(array $images): void {
        $oldSliderImages = $this->getSliderImages()->keyBy('id');
        $oldSliderImagesKeys = $oldSliderImages->keys()->toArray();
        $newSliderImagesKeys = [];

        if (!empty($images)) {
            foreach ($images as $imageArray) {
                $newSliderImageOriginal = $imageArray['id'];
                $newSliderImagesKeys[] = $newSliderImageOriginal;

                if (isset($oldSliderImages[$newSliderImageOriginal])) {
                    continue;
                }

                foreach ($imageArray as $key => $image) {
                    if ($key === 'src') {
                        continue;
                    }

                    if ($key === 'id') {
                        $newSliderImage = Image::whereKey($image)->first();
                        (is_null($newSliderImage))
                            ?: $this->images()->save($newSliderImage); //Сохраняем только если изображение найдено
                        continue;
                    }

                    $newSliderImage = Image::whereKey($image['id'])->first();
                    (is_null($newSliderImage))
                        ?: $this->images()->save($newSliderImage);
                }
            }

            $oldSliderImagesForDelete = array_diff($oldSliderImagesKeys, $newSliderImagesKeys);
            foreach ($oldSliderImagesForDelete as $coverId) {
                /**
                 * @var Image $oldSliderImageForDelete
                 */
                $oldSliderImageForDelete = $oldSliderImages[$coverId];
                $oldSliderImageForDelete->delete();
            }
        } else {
            $oldSliderImagesForDelete = $oldSliderImagesKeys;
            foreach ($oldSliderImagesForDelete as $coverId) {
                /**
                 * @var Image $oldSliderImageForDelete
                 */
                $oldSliderImageForDelete = $oldSliderImages[$coverId];
                $oldSliderImageForDelete->delete();
            }
        }
    }

    /**
     * @param string      $file
     * @param string      $namespace
     * @param string|null $prefixType
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function saveImage(string $file, string $namespace, string $prefixType = null): array {
        /**
         * @var Image $cover
         */
        $cover = new Image();
        $mainImages = [];
        [$defaultMaxWidth, $widths, $prefix] = self::getImageParameters($namespace, $prefixType);

        if (Image::isSVG($file)) {
            if (!Image::isBase64($file)) {
                $file = Image::getBase64WithMimeType($file);
            }

            $cover->downloadSVG($file, $prefix);
            $coverId = $cover->getKey();
            $mainImages['src'] = $cover->getPublishPath();
            $mainImages['id'] = $coverId;
        } else {
            $file = $cover->getFileSource($file);

            if (!is_null($defaultMaxWidth)) {
                //Если $defaultMaxWidth !== null, то устанавливаем персональное значение,
                //в противном случае будет использоваться значение, выставленное в модели по умолчанию
                $cover->setMaxWidth($defaultMaxWidth);
                $cover->setMaxHeight(null);
            }

            $cover->download($file, $prefix . $defaultMaxWidth);
            $coverId = $cover->getKey();
            $mainImages['src'] = $cover->getPublishPath();
            $mainImages['id'] = $coverId;

            //$widths = [1440, 1024, 800, 640, 480];
            foreach ($widths as $width) {
                $coverLess = new Image();
                $coverLess->setMaxWidth($width);
                $coverLess->setMaxHeight(null);
                $coverLess->download($file, $prefix . $width);
                $coverLess->parent()->associate($cover);
                $coverLess->save();
                $mainImages[$width] = [
                    'src' => $coverLess->getPublishPath(),
                    'id'  => $coverLess->getKey(),
                ];
            }
        }

        return $mainImages;
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public static function getImageParameters(string $namespace, ?string $prefixType): array {
        switch ($namespace) {
            case Organization::class:
                {
                    $prefix = 'cover_';
                    $defaultMaxWidth = 1920;
                    $widths = [1440, 1024, 800, 640, 480];
                }
                break;
            case Product::class:
                {
                    $prefix = 'product_image_';
                    $defaultMaxWidth = 765;
                    $widths = [580, 480];
                }
                break;
            case Article::class:
                {
                    $prefix = ($prefixType === 'text') ? 'text_' : 'cover_';
                    $defaultMaxWidth = null;
                    $widths = [];
                }
                break;
            default:
                {
                    $prefix = '';
                    $defaultMaxWidth = null;
                    $widths = [];
                }
                break;
        }

        return [$defaultMaxWidth, $widths, $prefix];
    }

    /**
     * @param string $file
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function saveLogo(string $file): array {
        $avatar = new Image();

        if (Image::isSVG($file)) {
            if (!Image::isBase64($file)) {
                $file = Image::getBase64WithMimeType($file);
            }

            $avatar->downloadSVG($file);
        } else {
            $avatar->download($file);
        }

        return [
            'id'  => $avatar->getKey(),
            'src' => $avatar->getPublishPath(),
        ];
    }

    /**
     * @param string $file
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function saveMiniLogo(string $file): array {
        $miniLogo = new Image();

        if (Image::isSVG($file)) {
            if (!Image::isBase64($file)) {
                $file = Image::getBase64WithMimeType($file);
            }

            $miniLogo->downloadSVG($file);
        } else {
            $miniLogo->download($file);
        }

        return [
            'id'  => $miniLogo->getKey(),
            'src' => $miniLogo->getPublishPath(),
        ];
    }
}
