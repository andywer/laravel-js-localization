<?php
namespace JsLocalization\Caching;

use Cache;
use Config;
use Event;
use Lang;
use JsLocalization\Facades\JsLocalizationHelper;

/**
 * Class MessageCachingService
 * @package JsLocalization\Caching
 */
class MessageCachingService extends AbstractCachingService
{

    /**
     * The key used to cache the JSON encoded messages.
     *
     * @var string
     */
    const CACHE_KEY = 'js-localization-messages-json';

    /**
     * The key used to cache the timestamp of the last
     * refreshCache() call.
     *
     * @var string
     */
    const CACHE_TIMESTAMP_KEY = 'js-localization-messages-last-modified';

    
    public function __construct()
    {
        parent::__construct(self::CACHE_KEY, self::CACHE_TIMESTAMP_KEY);
    }

    /**
     * Returns the messages (already JSON encoded).
     * Refreshes the cache if necessary.
     *
     * @param bool $noCache (optional) Defines if cache should be ignored.
     * @return string JSON encoded messages object.
     */
    public function getMessagesJson($noCache = false)
    {
        if ($noCache) {
            return $this->createMessagesJson();
        } else {
            return $this->getData();
        }
    }

    /**
     * Refreshes the cache item containing the JSON encoded
     * messages object.
     * Fires the 'JsLocalization.registerMessages' event.
     *
     * @return void
     */
    public function refreshCache()
    {
        Event::fire('JsLocalization.registerMessages');

        $messagesJSON = $this->createMessagesJson();
        $this->refreshCacheUsing($messagesJSON);
    }

    /**
     * @return string
     */
    protected function createMessagesJson()
    {
        $locales = $this->getLocales();
        $messageKeys = $this->getMessageKeys();
        $translatedMessages = $this->getTranslatedMessagesForLocales($messageKeys, $locales);

        return json_encode($translatedMessages);
    }

    /**
     * Returns the locales we need to consider.
     *
     * @return array Locales.
     */
    protected function getLocales()
    {
        return Config::get('js-localization.locales');
    }

    /**
     * Returns the translated messages for the given keys and locales.
     *
     * @param array $messageKeys
     * @param array $locales
     * @return array The translated messages as [<locale> => [ <message id> => <translation>, ... ], ...]
     */
    protected function getTranslatedMessagesForLocales(array $messageKeys, array $locales)
    {
        $translatedMessages = [];

        foreach ($locales as $locale) {
            if (!isset($translatedMessages[$locale])) {
                $translatedMessages[$locale] = [];
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
     * @return array The translated messages as [ <message id> => <translation>, ... ]
     */
    protected function getTranslatedMessages(array $messageKeys, $locale)
    {
        $translatedMessages = [];

        foreach ($messageKeys as $key) {
            $translation = Lang::get($key, [], $locale);

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
    protected function flattenTranslations(array $nestedMessages, $keyPrefix='')
    {
        $flattened = [];

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
    protected function getMessageKeys()
    {
        $messageKeys = Config::get('js-localization.messages');
        $messageKeys = JsLocalizationHelper::resolveMessageKeyArray($messageKeys);

        $messageKeys = array_unique(
            array_merge($messageKeys, JsLocalizationHelper::getAdditionalMessages())
        );

        return $messageKeys;
    }
}
