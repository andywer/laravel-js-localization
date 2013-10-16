<?php

use JsLocalization\CachingService;
use JsLocalization\JsLocalizationHelper;

App::singleton('JsLocalizationHelper', function()
{
    return new JsLocalizationHelper;
});

App::singleton('JsLocalizationCachingService', function()
{
    return new CachingService;
});
