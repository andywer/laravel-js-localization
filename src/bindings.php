<?php

use JsLocalization\Caching\MessageCachingService;
use JsLocalization\Utils\Helper;

App::singleton('JsLocalizationHelper', function()
{
    return new Helper;
});

App::singleton('JsLocalizationMessageCachingService', function()
{
    return new MessageCachingService;
});
