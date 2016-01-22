<?php

namespace JsLocalization\Http\Controllers;

use Illuminate\Routing\Controller;
use JsLocalization\Facades\ConfigCachingService;
use JsLocalization\Facades\MessageCachingService;
use JsLocalization\Facades\TranslationData;
use JsLocalization\Http\Responses\StaticFileResponse;

class JsLocalizationController extends Controller
{

    /**
     * Create the JS-Response for all configured translation messages
     *
     * @return \Illuminate\Http\Response
     */
    public function createJsMessages()
    {
        $contents = TranslationData::getMessagesJson();

        return response($contents)
            ->header('Content-Type', 'text/javascript')
            ->setLastModified(MessageCachingService::getLastRefreshTimestamp());
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function createJsConfig()
    {
        $contents = TranslationData::getConfigJson();

        /** @var \Illuminate\Http\Response $response */
        $response = response($contents);
        $response->header('Content-Type', 'text/javascript');

        if (ConfigCachingService::isDisabled()) {
            $response->setEtag(md5($contents));
        } else {
            $response->setLastModified(ConfigCachingService::getLastRefreshTimestamp());
        }

        return $response;
    }

    /**
     * Deliver the Framework for getting the translation in JS
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function deliverAllInOne()
    {
        $contents = file_get_contents( __DIR__."/../../../public/js/localization.min.js" );
        $contents .= "\n";
        $contents .= TranslationData::getMessagesJson();
        $contents .= TranslationData::getConfigJson();

        /** @var \Illuminate\Http\Response $response */
        $response = response($contents);
        $response->header('Content-Type', 'text/javascript');

        if (ConfigCachingService::isDisabled()) {
            $response->setEtag(md5($contents));
        } else {
            $response->setLastModified(MessageCachingService::getLastRefreshTimestamp());
        }

        return $response;
    }

}
