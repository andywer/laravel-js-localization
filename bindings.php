<?php

use JsLocalization\CachingService;
use JsLocalization\Helper;

App::singleton('JsLocalizationHelper', function()
{
    return new Helper;
});

App::singleton('JsLocalizationCachingService', function()
{
    return new CachingService;
});
