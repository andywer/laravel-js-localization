<?php
namespace JsLocalization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class MessageCachingService
 * @package JsLocalization\Facades
 * 
 * @method static void refreshCache()
 * @method static \DateTime getLastRefreshTimestamp()
 * @method static string getMessagesJson(bool $noCache = false)
 * @method static void public function refreshCache()
 */
class MessageCachingService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'JsLocalizationMessageCachingService';
    }
}