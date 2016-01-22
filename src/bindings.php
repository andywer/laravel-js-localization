<?php

use JsLocalization\Caching\ConfigCachingService;
use JsLocalization\Caching\MessageCachingService;
use JsLocalization\Output\TranslationData; 
use JsLocalization\Utils\Helper;

App::singleton('JsLocalizationHelper', function()
{
    return new Helper;
});

App::singleton('JsLocalizationMessageCachingService', function()
{
    return new MessageCachingService;
});

App::singleton('JsLocalizationConfigCachingService', function()
{
    return new ConfigCachingService();
});

App::singleton('TranslationData', function()
{
    return new TranslationData();
});
