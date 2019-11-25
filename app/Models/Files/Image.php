<?php

namespace App\Models\Files;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * App\Models\Files\Image
 *
 * @property int                                                                     $id
 * @property int|null                                                                $imagable_id
 * @property string|null                                                             $imagable_type
 * @property string|null                                                             $public_path
 * @property string|null                                                             $local_path
 * @property string|null                                                             $name
 * @property string|null                                                             $alt
 * @property int|null                                                                $width
 * @property int|null                                                                $height
 * @property \Carbon\Carbon|null                                                     $created_at
 * @property \Carbon\Carbon|null                                                     $updated_at
 * @property string|null                                                             $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent                      $imagable
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Image onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereImagableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereImagableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereLocalPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image wherePublicPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Image withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Image withoutTrashed()
 * @mixin \Eloquent
 * @property string|null                                                             $mime
 * @property int|null                                                                $size
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image filter($frd)
 * @property int|null                                                                $user_id
 * @property string|null                                                             $fileable_type
 * @property string|null                                                             $fileable_id
 * @property array|null                                                              $payload
 * @property string|null                                                             $file_delete_at
 * @property string|null                                                             $file_deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[] $fileable
 * @property-read \App\Models\Users\User|null                                        $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereFileDeleteAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereFileDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereFileableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereFileableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereUserId($value)
 * @method static bool|null forceDelete()
 * @property int|null                                                                $file_parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[] $children
 * @property-read \App\Models\Files\Image|null                                       $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Image whereFileParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File unusedImages()
 */
class Image extends File {

    use SoftDeletes {
        forceDelete as traitForceDelete;
    }

    /**
     * @var int
     */
    protected $maxWidth = 1240;

    /**
     * @var int
     */
    protected $maxWidthThumb = 300;

    /**
     * @var int
     */
    protected $maxHeight = 900;

    /**
     * @var int
     */
    protected $maxHeightThumb = 300;
    /**
     * @var int
     */
    private $quality = 75;

    /**
     * @var ImageManager
     */
    private $imageManager = null;

    /**
     * @var FilesystemAdapter
     */
    private $storage = null;

    /**
     * @var string
     */
    public $imageManagerDriver = 'imagick';

    /**
     *
     */
    public static function boot() {
        parent::boot();
        self::deleting(function (Image $image) {
            foreach ($image->children as $_image) {
                $_image->delete();
            }
        });
    }


    /**
     * @return string
     */
    public function getImageManagerDriver(): string {
        return $this->imageManagerDriver;
    }

    /**
     * @param string $image
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function isSVG(string $image): bool {
        /**
         * @var string $source
         */
        $file = new Image();
        $typeFile = $file->getFileType($image);

        if ($typeFile === 'base64') {
            if (false !== stripos($image, 'svg+xml')) {
                return true;
            }
        } elseif ($typeFile === 'url' || $typeFile === 'uploadedFile') {
            $source = $file->getFileSource($image);
            $f = finfo_open();
            $mimeType = finfo_buffer($f, $source, FILEINFO_MIME_TYPE);

            if ($mimeType === 'image/svg' || $mimeType === 'image/svg+xml') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param             $file
     * @param string|null $prefix
     * @param Carbon|null $fileDeleteAt
     * @param string      $extension
     * @param int         $quality
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadSVG($file, string $prefix = null, Carbon $fileDeleteAt = null, string $extension = 'svg', int $quality = 100): bool {
        $result = false;
        try {

            /**
             * Внутренний путь файлового хранилища
             *
             * @var string $internalPath
             */
            $internalPath = $this->getInternalPath($file, $prefix, 'images', $extension);

            /**
             * @var string $source
             */
            $source = $this->getFileSource($file);
            $source = str_replace('data:image/svg+xml;base64,', '', $source);
            $source = base64_decode($source);

            /**
             * Записываем на файловое хранилище
             */
            $result = $this->put($internalPath, $source, ['ContentType' => 'image/svg+xml']);

            if (true === $result) {
                /**
                 * Заливаший пользователь, если не задан
                 */
                if (null === $this->getUserId() && auth()->check()) {
                    $this->setUserId(auth()->id());
                }

                /**
                 * Устанавливаем имя файла, если не задано
                 */
                if (null === $this->getName()) {
                    $this->setName($this->getFileName($file));
                }

                /**
                 * Выставляем дату удаления (ее джоба будет проверять и удалять старые пикчи)
                 */
                if (null !== $fileDeleteAt) {
                    $this->setFileDelete($fileDeleteAt);
                }


                $this->init($internalPath);
                $this->save();
            }

        } catch (\Exception $exception) {
            Log::critical('svg@download ' . $exception->getMessage(), (array)$exception);
        }

        return $result;
    }

    /**
     * @param             $file
     * @param string|null $prefix
     * @param Carbon|null $fileDeleteAt
     * @param string      $extension
     * @param int         $quality
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download($file, string $prefix = null, Carbon $fileDeleteAt = null, string $extension = 'jpg', int $quality = 100): bool {
        $result = false;
        try {

            /**
             * Внутренний путь файлового хранилища
             *
             * @var string $internalPath
             */
            $internalPath = $this->getInternalPath($file, $prefix, 'images', $extension);

            /**
             * Обрезаем и сжимаем изображение
             *
             * @var ImageManager $image
             */
            $imageManager = $this->prepareImageFromFile($file);

            if (null !== $imageManager) {
                /**
                 * @var string $image
                 */
                $image = $this->encodeImage($imageManager, $extension, $quality);

                /**
                 * Записываем на файловое хранилище
                 */
                $result = $this->put($internalPath, $image);

                /**
                 * Записываем параметры картинки (обязательно после записи в файловое хранилище)
                 */
                $this->initImage($imageManager);

                if (true === $result) {
                    /**
                     * Заливаший пользователь, если не задан
                     */
                    if (null === $this->getUserId() && auth()->check()) {
                        $this->setUserId(auth()->id());
                    }

                    /**
                     * Устанавливаем имя файла, если не задано
                     */
                    if (null === $this->getName()) {
                        $this->setName($this->getFileName($file));
                    }

                    /**
                     * Выставляем дату удаления (ее джоба будет проверять и удалять старые пикчи)
                     */
                    if (null !== $fileDeleteAt) {
                        $this->setFileDelete($fileDeleteAt);
                    }


                    $this->init($internalPath);
                    $this->save();
                }
            }

        } catch (\Exception $exception) {
            Log::critical('Image@download', [
                'message' => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'code'    => $exception->getCode(),
                'file'    => $exception->getFile(),
            ]);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getPayload(): array {
        return $this->{'payload'};
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int {
        return $this->getPayload()['width'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int {
        return $this->getPayload()['height'] ?? null;
    }


    /**
     * @param $file
     *
     * @return \Intervention\Image\Image|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function prepareImageFromFile($file): ?\Intervention\Image\Image {

        /**
         * @var string $source
         */
        $source = $this->getFileSource($file);

        /**
         * @var ImageManager $manager
         */
        $manager = $this->getImageManager();

        try {
            if (null !== $manager) {
                $image = $manager->make($source);
                $image = $this->reduceImage($image, $this->getMaxWidth(), $this->getMaxHeight());
                $image->getCore()->stripImage();
            }
        } catch (\Exception $exception) {
            Log::critical('Image@prepareImageFromFile', [
                'message' => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'code'    => $exception->getCode(),
            ]);
        }

        return $image ?? null;
    }

    /**
     * @param \Intervention\Image\Image $imageManager
     */
    public function initImage(\Intervention\Image\Image $imageManager): void {
        $payload = [
            'width'  => $imageManager->width(),
            'height' => $imageManager->height(),
        ];
        $this->setPayload($payload);
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param string                    $extension
     * @param int                       $quality
     *
     * @return string
     */
    public function encodeImage(\Intervention\Image\Image $image, string $extension = 'jpg', int $quality = 100): string {
        return (string)$image->encode($extension, $quality);
    }


    /**
     * @return ImageManager
     */
    public function getImageManager(): ImageManager {
        $imageManager = $this->imageManager;
        if (null === $imageManager) {

            $imageManager = new ImageManager(['driver' => $this->getImageManagerDriver()]);
            $this->imageManager = $imageManager;

        }

        return $imageManager;
    }

    /**
     * @param          $image
     * @param int      $width
     * @param int|null $height
     *
     * @return mixed
     */
    public function reduceImage($image, int $width, ?int $height) {
        if ($image->width() > $width || $image->height() > $height) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $image;
    }

    /**
     * @return int|null
     */
    public function getMaxWidth(): ?int {
        return $this->maxWidth;
    }

    /**
     * @param int|null $maxWidth
     */
    public function setMaxWidth(?int $maxWidth): void {
        $this->maxWidth = $maxWidth;
    }

    /**
     * @return int|null
     */
    public function getMaxHeight(): ?int {
        return $this->maxHeight;
    }

    /**
     * @param int|null $maxHeight
     */
    public function setMaxHeight(?int $maxHeight): void {
        $this->maxHeight = $maxHeight;
    }

    /**
     * @return int
     */
    public function getQuality(): int {
        return $this->quality;
    }

    /**
     * @param int $quality
     */
    public function setQuality(int $quality): void {
        $this->quality = $quality;
    }


    /**
     * @return int
     */
    public function getPerPagePublic(): int {
        return 4;
    }

    /**
     * @param Image $image
     * @param int   $width
     *
     * @return Image
     */
    public function reduceImageWidth($image, int $width) {
        if ($image->width() > $width) {
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $image;
    }

    /**
     * @param Image $image
     * @param int   $height
     *
     * @return Image
     */
    public function reduceImageHeight($image, int $height) {
        if ($image->height() > $height) {
            $image->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $image;
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function compressSafely($image) {
        $image->getCore()->stripImage();

        return $image;
    }

    /**
     * @return null|string
     */
    public function getPublishPath() {
        return $this->public_path;
    }

    /**
     * @return null|string
     */
    public function getAlt() {
        return $this->alt;
    }

    /**
     * @return bool
     */
    public function hasWightHeight() {
        return ($this->height !== null && $this->height > 0 && $this->width !== null && $this->width > 0);
    }

    /**
     * @return mixed
     */
    public function getProportions() {
        return str_replace(',', '.', (string)($this->height / $this->width * 100));
    }

    /**
     * @return bool|null
     */
    public function forceDelete() {
        $result = $this->eraseFromStorage();
        //$this->forceDelete();
        //return parent::forceDelete(); // TODO: Change the autogenerated stub
        return $this->traitForceDelete();
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo {
        return $this->belongsTo(self::class, 'file_parent_id');
    }

    /**
     * @return Image|null
     */
    public function getParent(): ?Image {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function hasParent(): bool {
        $parent = $this->getParent();

        return !is_null($parent);
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany {
        return $this->hasMany(self::class, 'file_parent_id');
    }

    /**
     * @return Collection
     */
    public function getChildren(): Collection {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool {
        $children = $this->getChildren();

        return !empty($children);
    }
}
