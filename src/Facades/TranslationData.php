<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 22.01.16
 * Time: 10:26
 */

namespace JsLocalization\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class TranslationData
 * @package JsLocalization\Facades
 * 
 * @method static string getMessagesJson()
 * @method static string getConfigJson()
 */
class TranslationData extends Facade {
    protected static function getFacadeAccessor() {
        return 'TranslationData';
    }
}