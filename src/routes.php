<?php

use JsLocalization\StaticFileResponse;

Route::get('/js-localization/messages', 'JsLocalizationController@createJsMessages');

Route::get('/js-localization/localization.js', function()
{
    $response = new StaticFileResponse( __DIR__."/../public/js/localization.min.js" );
    $response->setPublic();
    $response->header('Content-Type', 'application/javascript');

    return $response;
});
