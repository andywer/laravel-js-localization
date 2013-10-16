<?php

use JsLocalization\Facades\CachingService;

class JsLocalizationController extends Controller
{
    
    public function createJsMessages ()
    {
        $messages = CachingService::getMessagesJson();

        $contents  = 'Lang.setLocale("'.Lang::locale().'");';
        $contents .= 'Lang.addMessages('.$messages.');';

        $lastModified = new DateTime();
        $lastModified->setTimestamp(CachingService::getLastRefreshTimestamp());

        return Response::make($contents)
                ->header('Content-Type', 'text/javascript')
                ->header('Last-Modified', $lastModified->format('D, d M Y H:i:s T'));
    }

}