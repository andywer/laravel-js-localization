<?php

use JsLocalization\StaticFileResponse;

Route::get('/js-localization/messages', 'JsLocalizationController@createJsMessages');

Route::get('/js-localization/localization.js', function()
{
    return new StaticFileResponse( __DIR__."/public/js/localization.min.js" );
});
