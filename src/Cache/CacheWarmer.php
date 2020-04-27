<?php

namespace App\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use App\Feeds\FeedCreator;

class CacheWarmer implements CacheWarmerInterface
{
    private $feedCreator;

    public function __construct(FeedCreator $feedCreator)
    {
        $this->feedCreator = $feedCreator;
    }

    /**
     * Load cache before user hit web
     * @param string $cacheDirectory
     */
    public function warmUp($cacheDirectory)
    {
        $this->feedCreator->create();
    }

    public function isOptional()
    {
        return true;
    }
}
