<?php

class JsLocalizationController extends Controller
{
    
    public function createJsMessages ()
    {
        // TODO:
        $messages = array();

        $contents  = 'Lang.setLocale("'.Lang::locale().'");';
        $contents .= 'Lang.addMessages('.json_encode($messages).');';

        return Response::make($contents)
                ->header('Content-Type', 'text/javascript');
    }

}