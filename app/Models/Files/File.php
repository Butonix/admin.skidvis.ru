<?php

namespace App\Models\Files;

use App\Models\Users\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Util\MimeType;

/**
 * App\Models\Files\File
 *
 * @property int                                                $id
 * @property int                                                $user_id
 * @property string|null                                        $fileable_type
 * @property string|null                                        $fileable_id
 * @property string|null                                        $public_path
 * @property string|null                                        $local_path
 * @property string|null                                        $name
 * @property string|null                                        $mime
 * @property int|null                                           $size
 * @property array|null                                         $payload
 * @property \Illuminate\Support\Carbon|null                    $created_at
 * @property \Illuminate\Support\Carbon|null                    $updated_at
 * @property string|null                                        $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $fileable
 * @property-read \App\Models\Users\User                        $user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\File onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereFileableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereFileableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereLocalPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File wherePublicPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\File withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\File withoutTrashed()
 * @mixin \Eloquent
 * @property string|null                                        $delete_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereDeleteAt($value)
 * @property string|null                                        $file_delete_at
 * @property string|null                                        $file_deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereFileDeleteAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereFileDeletedAt($value)
 * @method static bool|null forceDelete()
 * @property int|null                                           $file_parent_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File whereFileParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File unusedImages()
 */
class File extends Model {
    use SoftDeletes {
        forceDelete as traitForceDelete;
    }

    protected $table = 'files';

    /**
     * @var array
     */
    protected $fillable = [
        'file_parent_id',
        'user_id',
        'fileable_id',
        'fileable_type',
        'public_path',
        'local_path',
        'name',
        'mime',
        'size',
        'payload',
        'file_delete_at',
        'file_deleted_at',
    ];

    /**
     * @var string
     */
    public $disk = 's3';
    //public $disk = 'local';

    /**
     * @param string $publicPath
     */
    public function setPublicPath(string $publicPath): void {
        $this->{'public_path'} = $publicPath;
    }

    /**
     * @param string $localPath
     */
    public function setLocalPath(string $localPath): void {
        $this->{'local_path'} = $localPath;
    }

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void {
        $this->{'payload'} = $payload;
    }

    /**
     * @param string $internalPath
     * @param string $source
     * @param array  $metadata
     *
     * @return bool
     */
    public function put(string $internalPath, string $source, array $metadata = []): bool {
        $result = false;
        try {
            $result = $this->getStorage()->put($internalPath, $source, $metadata);
        } catch (\Exception $exception) {
            Log::critical('File@put ' . $exception->getMessage(), (array)$exception);
        }
        return $result;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void {
        $this->{'size'} = $size;
    }

    /**
     * @param string $mime
     */
    public function setMime(string $mime): void {
        $this->{'mime'} = $mime;
    }

    /**
     * @var FilesystemAdapter
     */
    private $storage;

    /**
     * @return string
     */
    public function getSizeFormatted(): string {
        $size = $this->getSize();
        if (null !== $size) {
            $size = round($size / 1024 / 1024, 2) . ' Мб';
        } else {
            $size = 'Размер не определен, возможно файл загружен с ошибкой';
        }

        return $size;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int {
        return $this->{'size'};
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable(): ?MorphTo {
        return $this->morphTo('fileable', 'fileable_type', 'fileable_id');
    }

    /**
     * @param             $file
     * @param string|null $prefix
     * @param Carbon|null $fileDeleteAt
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download($file, string $prefix = null, Carbon $fileDeleteAt = null): bool {
        $result = false;
        try {
            $storage = $this->getStorage();
            /**
             * Добавить проверку на наличие файла с таким именем
             */
            $internalPath = $this->getInternalPath($file, $prefix);

            $source = $this->getFileSource($file);

            $this->getStorage()->put($internalPath, $source);

            if (auth()->check() && null === $this->getUserId()) {
                $this->setUserId(auth()->id());
            }

            if (null === $this->getName()) {
                $this->setName($this->getFileName($file));
            }


            $this->{'mime'} = $storage->mimeType($internalPath);
            $this->{'size'} = $storage->getSize($internalPath);
            $this->{'public_path'} = $storage->url($internalPath);
            $this->{'local_path'} = $internalPath;
            $this->{'file_delete_at'} = $fileDeleteAt ?? Carbon::now()->addDays(7);
            $this->save();
            $result = true;
        } catch (\Exception $exception) {
            Log::critical($exception->getMessage(), (array)$exception);
        }

        return $result;
    }

    /**
     * @param Carbon $date
     */
    public function setFileDelete(Carbon $date): void {
        $this->{'file_delete_at'} = $date;
    }

    /**
     * @return FilesystemAdapter
     */
    public function getStorage(): FilesystemAdapter {
        $storage = $this->storage;
        if ($storage === null) {
            /**
             * @var FilesystemAdapter $storage
             */
            $storage = Storage::disk($this->getDisk());
            $this->storage = $storage;
        }
        return $storage;
    }

    /**
     * @return string
     */
    private function getDisk(): string {
        return $this->disk;
    }

    /**
     * @param string $internalPath
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function init(string $internalPath): void {
        $storage = $this->getStorage();
        $this->setMime($storage->mimeType($internalPath));
        $this->setSize($storage->getSize($internalPath));
        $this->setPublicPath($storage->url($internalPath));
        $this->setLocalPath($internalPath);
        $this->setDisk($this->getDisk());
    }

    public function setDisk(string $disk) {
        $this->{'disk'} = $disk;
    }

    /**
     * @param             $file
     * @param string|null $prefix
     * @param string|null $folder
     * @param string|null $extension
     *
     * @return string
     */
    public function getInternalPath($file, string $prefix = null, string $folder = null, string $extension = null): string {
        if (null !== $prefix) {
            $prefix = mb_strtolower($prefix, 'UTF-8');
            $prefix = trim($prefix);
            $prefix = Str::slug($prefix);
            $prefix .= '-';
        }

        $storage = $this->getStorage();

        $fullFileName = $this->getFileName($file);

        if (null === $extension) {
            $extension = $this::getExtensionFromPathInfo($fullFileName);
        }

        $name = $this->getName() ?? $this::getNameFromPathInfo($fullFileName);

        $fileName = Str::slug(mb_strimwidth(mb_strtolower($name, 'UTF-8'), 0, 55, null, 'UTF-8')) . '.' . $extension;

        $postfix = '';

        $i = null;

        do {
            $path = 'public/' . (null !== $folder
                    ? $folder . '/'
                    : null) . date('Y/m/d/') . $prefix . $postfix . $fileName;
            ++$i;
            $postfix = $i . '-';
        } while ($storage->exists($path));
        return $path;
    }

    /**
     * @param $file
     *
     * @return bool|null|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFileSource($file): ?string {
        $type = $this->getFileType($file);
        switch ($type) {
            case 'uploadedFile':
                {
                    /**
                     * @var UploadedFile $file
                     */
                    $source = file_get_contents($file);
                }
                break;
            case 'url':
                {
                    /**
                     * @var string $image
                     */
                    $source = $this->getHttpContents($file);
                }
                break;
            case 'realpath':
                {
                    //Log::debug('asdasdads', (array)$file);
                    //$source = file_get_contents($file);
                    $source = $file;
                }
                break;

            default:
                {
                    /**
                     * @var string $image
                     */
                    $source = $file;
                }
                break;
        }

        return $source;
    }

    /**
     * @param $image
     *
     * @return string
     */
    public function getFileType($image): string {
        $type = 'blob';

        if (is_uploaded_file($image)) {
            $type = 'uploadedFile';
        } elseif (\is_string($image)) {

            if (false !== filter_var($image, FILTER_VALIDATE_URL)) {
                $type = 'url';
            } elseif (true === stream_is_local($image)) {
                $type = 'realpath';
            } elseif (false !== stripos($image, 'base64')) {
                $type = 'base64';
            }
        }

        return $type;
    }

    /**
     * @return bool
     */
    public static function isBase64($data): bool {
        $data = preg_replace('#^data:image/[^;]+;base64,#', '', $data);
        $decoded_data = base64_decode($data, true);
        $encoded_data = base64_encode($decoded_data);
        if ($encoded_data !== $data)
            return false;
        //else if (!ctype_print($decoded_data))
        //    return false;

        return true;
    }

    /**
     * @param $data
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getBase64($data): string {
        $file = new self();
        $data = $file->getFileSource($data);
        $base64 = base64_encode($data);

        return $base64;
    }

    /**
     * @param $data
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getBase64WithMimeType($data): string {
        $file = new self();
        $data = $file->getFileSource($data);
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $data, FILEINFO_MIME_TYPE);
        $base64 = base64_encode($data);
        $src = 'data:' . $mimeType . ';base64,' . $base64;

        return $src;
    }

    /**
     * @param string $url
     *
     * @return null|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHttpContents(string $url): ?string {

        $return = null;

        $client = new Client([
            'timeout' => 10,
        ]);
        try {
            $response = $client->head($url);
            if ($response->getStatusCode() === 200) {
                $response = $client->request('GET', $url);
                $return = $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            Log::critical('file@getHttpContents failed', (array)$e);
        }

        return $return;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int {
        return $this->{'user_id'};
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void {
        $this->{'user_id'} = $userId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->{'name'};
    }

    /**
     * @param string|null $name
     */
    public function setName(string $name = null): void {
        $this->{'name'} = $name;
    }

    /**
     * @param $file
     *
     * @return null|string
     */
    public function getFileName($file): ?string {
        $source = null;
        $type = $this->getFileType($file);
        switch ($type) {
            case 'uploadedFile':
                {
                    /**
                     * @var UploadedFile $file
                     */
                    $source = $file->getClientOriginalName();

                }
                break;
            case 'url':
                {
                    $source = pathinfo($file)['basename'] ?? null;
                    /**
                     * @var string $image
                     */
                }
                break;

            case 'realpath':
                {
                    //$source = pathinfo($file)['basename'] ?? null;
                    $source = uniqid(null, false);
                }
                break;

            case 'base64':
                {
                    $source = uniqid(null, false);
                }
                break;

            case 'blob':
                {
                    $source = uniqid(null, false);
                }
                break;

            default:
                {
                    $source = '';
                    Log::warning('File@getFileName failed file type not detected ' . $type);
                    /**
                     * @var string $image
                     */
                }
                break;
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getNameForDowloadFile(): string {
        return $this->created_at->format('d-m-Y_h-i_') . str_slug($this->getNameWithoutExtenstion()) . '.' . $this->getExtension();
    }


    /**
     * @return string
     */
    public function getNameWithoutExtenstion(): string {
        return pathinfo($this->getName())['filename'];
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public static function getNameFromPathInfo(string $name): ?string {
        return pathinfo($name)['filename'] ?? null;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public static function getExtensionFromPathInfo(string $name): ?string {
        $extension = pathinfo($name)['extension'];
        if (false !== strpos($extension, '?')) {
            $extensionArray = explode('?', $extension);
            $extension = $extensionArray[0];
        }
        $extension = trim($extension);
        $extension = mb_strtolower($extension, 'UTF-8');
        return $extension;
    }

    /**
     * @return string
     */
    public function getExtension(): string {
        $mime = $this->getMime();

        if ($mime === 'text/plain') {
            $extension = strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
        } else {
            $mimes = MimeType::getExtensionToMimeTypeMap();
            $extension = array_search($this->getMime(), $mimes, true);
        }

        return $extension;
    }

    /**
     * @return null|string
     */
    public function getMime(): ?string {
        return $this->{'mime'};
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
                case 'file_parent_id':
                    {
                        $query->where(function (Builder $query) use ($value) {
                            if ($value === 'null') {
                                $query->whereNull('file_parent_id');
                            }
                        });
                    }
                    break;
                case 'public_path':
                    {
                        $query->where(function (Builder $query) use ($value) {
                            $query->where('public_path', 'like', '%' . $value . '%');
                        });
                    }
                    break;
                default:
                    {
                        if (true === \in_array($key, $this->getFillable(), true)) {
                            $query->where($key, $value);
                        }
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * @param bool $cache
     *
     * @return mixed|string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContents($cache = false) {
        if (true === $cache) {
            $cacheName = 'files.' . $this->getKey();
            $contents = Cache::get($cacheName);
            if (null === $contents) {
                $contents = $this->getHttpContents($this->getPublicPath());
                $date = Carbon::now()->addHour();
                Cache::put($cacheName, $contents, $date);
            }
        } else {
            $contents = $this->getHttpContents($this->getPublicPath());
        }


        return $contents;
    }

    /**
     * @return null|string
     */
    public function getPublicPath(): ?string {
        return $this->{'public_path'};
    }

    /**
     * @return string
     */
    public function getNameFormatted(): string {
        return $this->getName() . ' ' . $this->created_at->format('d.m.Y H:i');
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
     * @return bool
     */
    public function eraseFromStorage(): bool {
        return $this->getStorage()->delete($this->getLocalPath());
    }

    /**
     * @return null|string
     */
    public function getLocalPath(): ?string {
        return $this->{'local_path'};
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUnusedImages(Builder $query): Builder {
        return $query->where(function (Builder $query) {
            $query->whereNull('fileable_id')->where('created_at', '<', now()->subHour()->toDateTimeString());
        });
    }
}
