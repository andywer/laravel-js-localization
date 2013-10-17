<?php

use JsLocalization\StaticFileRequest;

Route::get('/js-localization/messages', 'JsLocalizationController@createJsMessages');

Route::get('/js-localization/localization.js', function()
{
    return new StaticFileRequest( __DIR__."/public/js/localization.min.js" );
});
