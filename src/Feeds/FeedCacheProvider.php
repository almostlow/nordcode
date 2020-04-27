<?php

namespace App\Feeds;

use App\Cache\CacheInterface;

class FeedCacheProvider
{
    private $cacheProvider;
    private $data;
    private $status;

    private const KEY = 'feed';

    private const SUCCESS_STATUS = 'success';

    private const TTL = 300; // 5 minutes

    public function __construct(CacheInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
        $this->data = [];
        $this->status = 'Unknown';
    }

    /**
     * Loading data from coin layer provider.
     * @return void
     */
    public function loadData():void
    {
        $data = $this->cacheProvider->get(self::KEY);
        if (!empty($data)) {
            $this->data = $data;
            $this->status = self::SUCCESS_STATUS;
        } else {
            $this->data = [];
        }
    }

    /**
     * Set cache of rates.
     *
     * @param $value
     * @param $ttl
     */
    public function set(array $value):void
    {
        $this->cacheProvider->set(self::KEY, $value, self::TTL);
    }

    /**
     * Getting data after data loading in loadData method.
     *
     * @return array
     */
    public function getData():array
    {
        return $this->data;
    }

    /**
     * Checking response status from api.
     */
    public function isSuccessfull():bool
    {
        return self::SUCCESS_STATUS === $this->status;
    }
}
