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
     * A property to remember the cache driver used by the app.
     * @var string
     */
    private $app_cache_driver = '';

    /**
     * A property to remember the cache path used by the app.
     * @var string
     */
    private $app_cache_path = '';

    /**
     * If we're using commitable_cache option then set the cache
     * to use file system and a custom path.
     *
     * @return void
     */
    public function __construct()
    {
        if (Config::get('js-localization::config.commitable_cache')) {
            $this->app_cache_driver = Config::get('cache.driver');
            $this->app_cache_path = Config::get('cache.path');
            Config::set('cache.driver', 'file');
            Config::set('cache.path', app_path('database/js-localization'));
        }
    }

    /**
     * If we're using commitable_cache option then change the cache
     * settings back to what they were before implementing this class.
     *
     * @return void
     */
    public function __destruct()
    {
        if (Config::get('js-localization::config.commitable_cache')) {
            Config::set('cache.driver', $this->app_cache_driver);
            Config::set('cache.path',$this->app_cache_path);
        }
    }

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

        $locales = $this->getLocales();
        $messageKeys = $this->getMessageKeys();
        $translatedMessages = $this->getTranslatedMessagesForLocales($messageKeys, $locales);

        Cache::forever(self::CACHE_KEY, json_encode($translatedMessages));
        Cache::forever(self::CACHE_TIMESTAMP_KEY, time());
    }

    /**
     * Returns the UNIX timestamp of the last
     * refreshMessageCache() call.
     *
     * @return int UNIX timestamp
     */
    public function getLastRefreshTimestamp ()
    {
        return Cache::get(self::CACHE_TIMESTAMP_KEY);
    }

    /**
     * Returns the locales we need to consider.
     *
     * @return array Locales.
     */
    protected function getLocales ()
    {
        return Config::get('js-localization::config.locales');
    }

    /**
     * Returns the translated messages for the given keys and locales.
     *
     * @param array $messageKeys
     * @param array $locales
     * @return array The translated messages as array(<locale> => array( <message id> => <translation>, ... ), ...)
     */
    protected function getTranslatedMessagesForLocales (array $messageKeys, array $locales)
    {
        $translatedMessages = array();

        foreach ($locales as $locale) {
            if (!isset($translatedMessages[$locale])) {
                $translatedMessages[$locale] = array();
            }

            $translatedMessages[$locale] = $this->getTranslatedMessages($messageKeys, $locale);
        }

        return $translatedMessages;
    }

    /**
     * Returns the translated messages for the given keys.
     *
     * @param array $messageKeys
     * @param $locale
     * @return array The translated messages as array( <message id> => <translation>, ... )
     */
    protected function getTranslatedMessages (array $messageKeys, $locale)
    {
        $translatedMessages = array();

        foreach ($messageKeys as $key) {
            $translation = Lang::get($key, array(), $locale);

            if (is_array($translation)) {
                $flattened = $this->flattenTranslations($translation, $key.'.');
                $translatedMessages = array_merge($translatedMessages, $flattened);
            } else {
                $translatedMessages[$key] = $translation;
            }
        }

        return $translatedMessages;
    }

    /**
     * Transforms an array of nested translation messages into a "flat" (not nested) array.
     *
     * @param array $nestedMessages
     * @param string $keyPrefix
     * @return array Flattened translations array.
     */
    protected function flattenTranslations (array $nestedMessages, $keyPrefix='')
    {
        $flattened = array();

        foreach ($nestedMessages as $key => $message) {
            if (is_array($message)) {
                $flattenedMessages = $this->flattenTranslations($message, $keyPrefix . $key . '.');
                $flattened = array_merge($flattened, $flattenedMessages);
            } else {
                $flattened[$keyPrefix.$key] = $message;
            }
        }

        return $flattened;
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
