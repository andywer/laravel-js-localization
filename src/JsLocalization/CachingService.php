<?php
namespace JsLocalization;

use \Cache;
use \Config;
use \Lang;

class CachingService
{
    
    /**
     * The key used to cache the JSON encoded messages.
     *
     * @var string
     */
    const CACHE_KEY = 'js-localization-messages-json';

    /**
     * The key used to cache the timestamp of the last
     * refreshMessageCache() call.
     *
     * @var string
     */
    const CACHE_TIMESTAMP_KEY = 'js-localization-last-modified';

    /**
     * Returns the cached messages (already JSON encoded).
     * Creates the neccessary cache item if neccessary.
     *
     * @return string JSON encoded messages object.
     */
    public function getMessagesJson ()
    {
        if (!Cache::has(self::CACHE_KEY)) {
            $this->refreshMessageCache();
        }
        
        return Cache::get(self::CACHE_KEY);
    }

    /**
     * Refreshs the cache item containing the JSON encoded
     * messages object.
     *
     * @return void
     */
    public function refreshMessageCache ()
    {
        $messageKeys = Config::get('js-localization::config.messages');
        $translatedMessages = array();

        foreach ($messageKeys as $index=>$key) {
            $this->resolveMessageKey($key, $index, function($qualifiedKey) use(&$translatedMessages)
                {
                    $translatedMessages[$qualifiedKey] = Lang::get($qualifiedKey);
                });
        }

        Cache::forever(self::CACHE_KEY, json_encode($translatedMessages));
        Cache::forever(self::CACHE_TIMESTAMP_KEY, time());
    }

    /**
     * Returns the UNIX timestamp of the last refreshMessageCache() call.
     *
     * @return UNIX timestamp
     */
    public function getLastRefreshTimestamp ()
    {
        return Cache::get(self::CACHE_TIMESTAMP_KEY);
    }

    /**
     * Returns the concatenation of prefix and key if the key
     * is a string. If the key is an array then the function
     * will recurse.
     *
     * @param mixed $key            An array item read from the configuration ('messages' array).
     * @param mixed $keyIndex       The array index of $key. Is neccessary if $key is an array.
     * @param callable $callback    A callback function: function($fullyQualifiedKey).
     * @param string $prefix        Optional key prefix.
     */
    private function resolveMessageKey ($key, $keyIndex, $callback, $prefix="")
    {
        if (is_array($key)) {
            $_prefix = $prefix ? $prefix.$keyIndex."." : $keyIndex.".";

            foreach ($key as $_index=>$_key) {
                $this->resolveMessageKey($_key, $_index, $callback, $_prefix);
            }

        } else {
            $callback($prefix.$key);
        }
    }

}