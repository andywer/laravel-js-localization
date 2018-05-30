<?php
namespace JsLocalization\Caching;

use Config;
use Event;

/**
 * Class ConfigCachingService
 * @package JsLocalization\Caching
 */
class ConfigCachingService extends AbstractCachingService
{

    /**
     * The key used to cache the JSON encoded messages.
     *
     * @var string
     */
    const CACHE_KEY = 'js-localization-config-json';

    /**
     * The key used to cache the timestamp of the last
     * refreshCache() call.
     *
     * @var string
     */
    const CACHE_TIMESTAMP_KEY = 'js-localization-config-last-modified';


    public function __construct()
    {
        parent::__construct(self::CACHE_KEY, self::CACHE_TIMESTAMP_KEY);
    }

    /**
     * Refreshes the cache item containing the JSON encoded
     * config object.
     * Fires the 'JsLocalization.registerConfig' event.
     *
     * @return void
     */
    public function refreshCache()
    {
        Event::fire('JsLocalization.registerConfig');
        
        $configJson = $this->createConfigJson();
        $this->refreshCacheUsing($configJson);
    }

    /**
     * Returns the config (already JSON encoded).
     * Refreshes the cache if necessary.
     *
     * @param bool $noCache (optional) Defines if cache should be ignored.
     * @return mixed|string string   The JSON-encoded config exports.
     */
    public function getConfigJson($noCache = false)
    {
        if ($noCache || $this->isDisabled()) {
            return $this->createConfigJson();
        } else {
            return $this->getData();
        }
    }

    /**
     * @return bool Is config caching disabled? `true` means that this class does not cache, but create the data on the fly.
     */
    public function isDisabled()
    {
        return Config::get('js-localization.disable_config_cache', false);
    }

    /**
     * @return string
     */
    protected function createConfigJson()
    {
        $propertyNames = Config::get('js-localization.config', []);
        $configArray = [];
        
        foreach ($propertyNames as $propertyName) {
            $configArray[$propertyName] = Config::get($propertyName);
        }
        
        return json_encode((object)$configArray);
    }
    
}