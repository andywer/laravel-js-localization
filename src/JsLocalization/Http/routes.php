<?php

use JsLocalization\Http\Responses\StaticFileResponse;

Route::group([ 'namespace' => '\JsLocalization\Http\Controllers' ], function()
{
    Route::get('/js-localization/messages', 'JsLocalizationController@createJsMessages');

    Route::get('/js-localization/localization.js', function()
    {
        $response = new StaticFileResponse( __DIR__."/../../../public/js/localization.min.js" );
        $response->setPublic();
        $response->header('Content-Type', 'application/javascript');

        return $response;
    });
});
