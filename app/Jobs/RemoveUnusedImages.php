<?php

namespace App\Jobs;

use App\Models\Files\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveUnusedImages implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var bool
     */
    private $cache;

    /**
     * @var
     */
    protected $images;

    /**
     * RemoveUnusedImages constructor.
     *
     * @param bool $debug
     * @param bool $cache
     */
    public function __construct(bool $debug = false, bool $cache = false) {
        $this->debug = $debug;
        $this->cache = $cache;
    }

    /**
     * @param Image $images
     */
    public function handle(Image $images) {
        //dump('handle hello');
        $this->images = $images;
        $count = 0;
        $unusedImages = $this->images->unusedImages()->chunk(100, function ($images) use (&$count){
            foreach ($images as $image) {
                /**
                 * @var Image $image
                 */
                //$count++;
                $image->forceDelete();
            }
        });

        //dump($count);
    }
}
