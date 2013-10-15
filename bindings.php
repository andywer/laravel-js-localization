<?php

use JsLocalization\CachingService;

App::singleton('JsLocalizationCachingService', function()
{
    return new CachingService;
});
