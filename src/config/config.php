<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Define the languages you want exported messages for
    |--------------------------------------------------------------------------
    */

    'locales' => array('en'),

    /*
    |--------------------------------------------------------------------------
    | Commitable cache
    |--------------------------------------------------------------------------
    |
    | Set to true to store the messages cache in a file that can be committed
    | to version control. This is so you don't need to remember to run
    | `php artisan js-localization:refresh` in your production environment
    | every time you push an update.
    |
    */

    'commitable_cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Define the messages to export
    |--------------------------------------------------------------------------
    |
    | An array containing the keys of the messages you wish to make accessible
    | for the Javascript code.
    | Remember that the number of messages sent to the browser influences the
    | time the website needs to load. So you are encouraged to limit these
    | messages to the minimum you really need.
    |
    | Supports nesting:
    |   array( 'mynamespace' => array( 'test1', 'test2' ) )
    | for instance will be internally resolved to:
    |   array('mynamespace.test1', 'mynamespace.test2')
    |
    */

    'messages' => array(),

);
