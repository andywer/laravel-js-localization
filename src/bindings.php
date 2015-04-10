<?php

use JsLocalization\Caching\CachingService;
use JsLocalization\Utils\Helper;

App::singleton('JsLocalizationHelper', function()
{
    return new Helper;
});

App::singleton('JsLocalizationCachingService', function()
{
    return new CachingService;
});
