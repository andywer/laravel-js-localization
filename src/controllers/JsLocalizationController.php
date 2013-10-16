<?php

class JsLocalizationController extends Controller
{
    
    public function createJsMessages ()
    {
        $cachingService = App::make('JsLocalizationCachingService');

        $messages = $cachingService->getMessagesJson();

        $contents  = 'Lang.setLocale("'.Lang::locale().'");';
        $contents .= 'Lang.addMessages('.$messages.');';

        $lastModified = new DateTime();
        $lastModified->setTimestamp($cachingService->getLastRefreshTimestamp());

        return Response::make($contents)
                ->header('Content-Type', 'text/javascript')
                ->header('Last-Modified', $lastModified->format('D, d M Y H:i:s T'));
    }

}