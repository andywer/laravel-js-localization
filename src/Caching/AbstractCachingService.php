<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 02.08.15
 * Time: 23:33
 */

namespace JsLocalization\Caching;

use Cache;
use DateTime;

abstract class AbstractCachingService {

    /**
     * The key used to cache the data.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * The key used to cache the timestamp of the last
     * refreshCache() call.
     *
     * @var string
     */
    protected $cacheTimestampKey;


    /**
     * @param string $cacheKey
     * @param string $cacheTimestampKey
     */
    public function __construct($cacheKey, $cacheTimestampKey)
    {
        $this->cacheKey = $cacheKey;
        $this->cacheTimestampKey = $cacheTimestampKey;
    }

    /**
     * Returns the timestamp of the last refreshCache() call.
     *
     * @return DateTime
     */
    public function getLastRefreshTimestamp()
    {
        $unixTime = Cache::get($this->cacheTimestampKey);
        
        $dateTime = new DateTime;
        $dateTime->setTimestamp($unixTime);
        
        return $dateTime;
    }

    /**
     * Refresh the cached data.
     *
     * @param mixed $data
     */
    public function refreshCacheUsing($data)
    {
        Cache::forever($this->cacheKey, $data);
        Cache::forever($this->cacheTimestampKey, time());
    }

    /**
     * Create up-to-date data and update cache using refreshCacheUsing().
     * Trigger some event.
     * 
     * @return void
     */
    abstract public function refreshCache();

    /**
     * Return the cached data. Refresh cache if cache has not yet been initialized.
     * 
     * @return mixed
     */
    public function getData()
    {
        if (!Cache::has($this->cacheKey)) {
            $this->refreshCache();
        }

        return Cache::get($this->cacheKey);
    }
    
}