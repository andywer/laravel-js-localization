<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 02.08.15
 * Time: 23:33
 */

namespace JsLocalization\Caching;

use Cache;

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
     * Returns the UNIX timestamp of the last
     * refreshCache() call.
     *
     * @return int UNIX timestamp
     */
    public function getLastRefreshTimestamp()
    {
        return Cache::get($this->cacheTimestampKey);
    }

    /**
     * Refresh the cached data.
     *
     * @return void
     */
    abstract public function refreshCache();
    
}