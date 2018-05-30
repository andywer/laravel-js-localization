<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 03.08.15
 * Time: 16:40
 */

namespace JsLocalization\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class ConfigCachingService
 * @package JsLocalization\Facades
 *
 * @method static void refreshCache()
 * @method static \DateTime getLastRefreshTimestamp()
 * @method static string getConfigJson(bool $noCache = false)
 * @method static bool isDisabled()
 * @method static void public function refreshCache()
 */
class ConfigCachingService extends Facade {
    protected static function getFacadeAccessor() {
        return 'JsLocalizationConfigCachingService';
    }
}