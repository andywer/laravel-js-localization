<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 22.01.16
 * Time: 10:24
 */

namespace JsLocalization\Output;

use JsLocalization\Facades\ConfigCachingService;
use JsLocalization\Facades\MessageCachingService;

class TranslationData {

    /**
     * Get the configured messages from the translation files
     *
     * @return string
     */
    public function getMessagesJson()
    {
        $messages = MessageCachingService::getMessagesJson();
        $messages = $this->ensureBackwardsCompatibility($messages);

        $contents  = 'Lang.addMessages(' . $messages . ');';

        return $contents;
    }

    /**
     * Get the JSON-encoded config properties that shall be passed to the client.
     *
     * @return string
     */
    public function getConfigJson()
    {
        $config = ConfigCachingService::getConfigJson();

        $contents = 'Config.addConfig(' . $config . ');';

        return $contents;
    }

    /**
     * Transforms the cached data to stay compatible to old versions of the package.
     *
     * @param string $messages
     * @return string
     */
    protected function ensureBackwardsCompatibility($messages)
    {
        if (preg_match('/^\\{"[a-z]{2}":/', $messages)) {
            return $messages;
        } else {
            return '{"' . app()->getLocale() . '":' . $messages . '}';
        }
    }

}