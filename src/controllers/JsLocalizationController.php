<?php

use JsLocalization\Facades\CachingService;

class JsLocalizationController extends Controller
{

    public function createJsMessages ()
    {
        $messages = CachingService::getMessagesJson();
        $messages = $this->ensureBackwardsCompatibility($messages);

        $contents  = 'Lang.addMessages(' . $messages . ');';
        $contents .= 'Lang.setLocale("' . Lang::locale() . '");';

        $lastModified = new DateTime();
        $lastModified->setTimestamp(CachingService::getLastRefreshTimestamp());

        return Response::make($contents)
                ->header('Content-Type', 'text/javascript')
                ->setLastModified($lastModified);
    }

    protected function ensureBackwardsCompatibility ($messages)
    {
        if(preg_match('/^\\{"[a-z]{2}":/', $messages)) {
            return $messages;
        } else {
            return '{"' . Lang::locale() . '":' . $messages . '}';
        }
    }

}
