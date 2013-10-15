<?php

class JsLocalizationController extends Controller
{
    
    public function createJsMessages ()
    {
        $cachingService = App::make('JsLocalizationCachingService');

        $messages = $cachingService->getMessagesJson();

        $contents  = 'Lang.setLocale("'.Lang::locale().'");';
        $contents .= 'Lang.addMessages('.$messages.');';

        return Response::make($contents)
                ->header('Content-Type', 'text/javascript');
    }

}