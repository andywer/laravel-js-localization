<?php

namespace JsLocalization\Http\Controllers;

use DateTime;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use JsLocalization\Facades\CachingService;
use JsLocalization\Http\Responses\StaticFileResponse;

class JsLocalizationController extends Controller
{

    /**
     * Create the JS-Response for all configured translation messages
     *
     * @return Http\Response
     */
    public function createJsMessages()
    {

        $contents = $this->getMessages();

        $lastModified = new DateTime();
        $lastModified->setTimestamp(CachingService::getLastRefreshTimestamp());

        return response($contents)
                ->header('Content-Type', 'text/javascript')
                ->setLastModified($lastModified);
    }

    /**
     * Deliver the Framework for getting the translation in JS
     *
     * @return Http\Response
     */
    public function deliverLocalizationJS()
    {
        $response = new StaticFileResponse( __DIR__."/../../../public/js/localization.min.js" );
        $response->setPublic();
        $response->header('Content-Type', 'application/javascript');

        return $response;
    }

    /**
     * Deliver one file that combines messages and framework.
     * Saves one additional HTTP-Request
     *
     * @return Http\Response
     */
    public function deliverLocalizationJSAndMessages()
    {
        $contents = file_get_contents( __DIR__."/../../../public/js/localization.min.js" );
        $contents .= "\n";
        $contents .= $this->getMessages();

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

    /**
     * Get the configured messages from the translation files
     *
     * @return string
     */
    private function getMessages()
    {
        $messages = CachingService::getMessagesJson();
        $messages = $this->ensureBackwardsCompatibility($messages);

        $contents  = 'Lang.addMessages(' . $messages . ');';
        $contents .= 'Lang.setLocale("' . Lang::locale() . '");';

        return $contents;
    }

}
