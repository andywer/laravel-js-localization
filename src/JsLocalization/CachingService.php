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

        $locales = $this->getLocales();
        $messageKeys = $this->getMessageKeys();
        $translatedMessages = $this->getTranslatedMessages($messageKeys, $locales);

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
    protected function getTranslatedMessages (array $messageKeys, array $locales)
    {
        $translatedMessages = array();

        foreach ($locales as $locale) {
            $translatedMessages[$locale] = array();
        }

        foreach ($locales as $locale) {
            foreach ($messageKeys as $key) {
                $translation = Lang::get($key, array(), $locale);

                if (is_array($translation)) {
                    $flattened = $this->flattenTranslations($translation, $key.'.');
                    $translatedMessages[$locale] = array_merge($translatedMessages[$locale], $flattened);
                } else {
                    $translatedMessages[$locale][$key] = $translation;
                }
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
                $flattenedMessage = $this->flattenTranslations($message, $keyPrefix . $key . '.');
                $flattened[$keyPrefix.$key] = $flattenedMessage;
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
