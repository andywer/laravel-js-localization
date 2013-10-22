<?php
namespace JsLocalization;

use Cache;
use Config;
use Lang;
use JsLocalization\Facades\JsLocalizationHelper;

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
     * Fires the 'JsLocalization.refresh' event.
     *
     * @return void
     */
    public function refreshMessageCache ()
    {
        JsLocalizationHelper::triggerRegisterMessages();

        $messageKeys = $this->getMessageKeys();
        $translatedMessages = array();

        foreach ($messageKeys as $key) {
            $translatedMessages[$key] = Lang::get($key);
        }

        Cache::forever(self::CACHE_KEY, json_encode($translatedMessages));
        Cache::forever(self::CACHE_TIMESTAMP_KEY, time());
    }

    /**
     * Returns the UNIX timestamp of the last
     * refreshMessageCache() call.
     *
     * @return UNIX timestamp
     */
    public function getLastRefreshTimestamp ()
    {
        return Cache::get(self::CACHE_TIMESTAMP_KEY);
    }

    /**
     * Returns the message keys of all messages
     * that are supposed to be sent to the browser.
     *
     * @return array Array of message keys.
     */
    protected function getMessageKeys ()
    {
        $messageKeys = Config::get('js-localization::config.messages');
        $messageKeys = JsLocalizationHelper::resolveMessageKeyArray($messageKeys);

        $messageKeys = array_unique(
            array_merge($messageKeys, JsLocalizationHelper::getAdditionalMessages())
        );

        return $messageKeys;
    }

}