<?php

namespace JsLocalization\Http\Controllers;

use DateTime;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use JsLocalization\Facades\CachingService;

class JsLocalizationController extends Controller
{

    public function createJsMessages()
    {
        $messages = CachingService::getMessagesJson();
        $messages = $this->ensureBackwardsCompatibility($messages);

        $contents  = 'Lang.addMessages(' . $messages . ');';
        $contents .= 'Lang.setLocale("' . Lang::locale() . '");';

        $lastModified = new DateTime();
        $lastModified->setTimestamp(CachingService::getLastRefreshTimestamp());

        return response($contents)
                ->header('Content-Type', 'text/javascript')
                ->setLastModified($lastModified);
    }

    protected function ensureBackwardsCompatibility($messages)
    {
        if (preg_match('/^\\{"[a-z]{2}":/', $messages)) {
            return $messages;
        } else {
            return '{"' . Lang::locale() . '":' . $messages . '}';
        }
    }

    public function deliverLocalizationJS()
    {
        $response = new StaticFileResponse( __DIR__."/../../public/js/localization.min.js" );
        $response->setPublic();
        $response->header('Content-Type', 'application/javascript');

        return $response;
    }

}
