laravel-js-localization
=======================
[![Build Status](https://travis-ci.org/andywer/laravel-js-localization.svg?branch=laravel-5)](https://travis-ci.org/andywer/laravel-js-localization) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/badges/quality-score.png?b=laravel-5)](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/?branch=laravel-5) [![Code Coverage](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/badges/coverage.png?b=laravel-5)](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/?branch=laravel-5) [![Total Downloads](https://poser.pugx.org/andywer/js-localization/downloads.svg)](https://packagist.org/packages/andywer/js-localization)


Simple, ease-to-use and flexible package for the [Laravel](http://laravel.com/) web framework. Allows you to use localized messages of the Laravel webapp (see `resources/lang` directory) in your Javascript code. You may easily configure which messages you need to export.

**⚠️ Looking for a new maintainer. Please contact me if you are interested.**


Branches
--------

 Laravel | Branch
:--------|:-------
 5.x     | laravel-5
 4.2     | laravel-4.2
 4.1     | laravel-4.1 (near end of life)
 4.0     | laravel-4.0 (end of life)


Installation
------------

Add the following line to the `require` section of your Laravel webapp's `composer.json` file:

```javascript
    "require": {
        "andywer/js-localization": "dev-laravel-5"    // "dev-laravel-4.1", "dev-laravel-4.2" for Laravel 4
    }
```


Run `composer update` to install the package.


Finally add the following line to the `providers` array of your `app/config/app.php` file:

```php
    'providers' => [
        /* ... */
        JsLocalization\JsLocalizationServiceProvider::class
    ]
```


Configuration
-------------

Run `php artisan vendor:publish` first. This command copies the package's default configuration to `config/js-localization.php`.

You may now edit this file to define the messages you need in your Javascript code. Just edit the `messages` array in the config file.

Example (exports all reminder messages):

```php
<?php

return [
    // Set the locales you use
    'locales' => ['en'],

    // Set the keys of the messages you want to use in javascript
    'messages' => [
        'passwords' => [
            'password', 'user', 'token'
        ]
    ],

    /*
     * in short:
     * 'messages' => ['passwords']
     *
     *
     * you could also use:
     *
     * 'messages' => [
     *     'passwords.password',
     *     'passwords.user',
     *     'passwords.token'
     * ]
     */
     
    // Set the keys of config properties you want to use in javascript.
    // Caution: Do not expose any configuration values that should be kept privately!
    'config' => [
        'app.debug'
    ],
     
    // Disables the config cache if set to true, so you don't have to run `php artisan js-localization:refresh`
    // each time you change configuration files.
    // Attention: Should not be used in production mode due to decreased performance.
    'disable_config_cache' => false,

    // Split up the exported messages.js file into separate files for each locale.
    // This is to ensue faster loading times so one doesn't have to load translations for _all_ languages.
    'split_export_files' => true,
];
```

__Important:__

The messages configuration will be cached when the JsLocalizationController is used for the first time. After changing the messages configuration you will need to call __`php artisan js-localization:refresh`__ to refresh that cache. That also affects the config properties you export to javascript, since they are cached, too.


Usage
-----

The translation resources for JavaScript can either be served by your Laravel app at run-time or they can be pre-generated as static JavaScript files, allowing you to serve them straight from your web server or CDN or to be included in your build process.

### Run-time generation

You just need to add the necessary `<script>` tags to your layout. Here is an example blade view:

```html
@include('js-localization::head')
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Test view</title>
        @yield('js-localization.head')
    </head>
    <body>
        <p>
            Here comes a translated message:
            <script type="text/javascript">
                document.write( Lang.get('reminder.user') );
            </script>
        </p>
    </body>
</html>
```

Remember it's best to not put the `@yield('js-localization.head')` in the `<head>` as it contains the `<script>` tag
shipping the frontend part of this package. It's best practice to put it at the end of the `<body>`, but **before**
other `<script>` tags. The example above simply includes it in the head, since it's the simplest form to use it. 

### Static generation

For increased performance it is possible to generate static JavaScript files with all of your generated strings. These files can either be served directly as static files, or included as a part of your frontend asset build process.

To specify the output directory for the assets, just set the `$storage_path` string in your `config/js-localization.php` file accordingly (see [Configuration](#configuration)).

```
    /*
    |--------------------------------------------------------------------------
    | Define the target to save the exported messages to
    |--------------------------------------------------------------------------
    |
    | Directory for storing the static files generated when using file storage.
    |
    */

    'storage_path' => public_path('vendor/js-localization/'),
```

The files can then be generated using the artisan command:

`php artisan js-localization:export`

This will generate two files in your target directory:
 * `messages.js` contains your translation strings
 * `config.js` contains your exported config values

If you want to automatically split up the `messages.js` file into separate .js files for each locale, you can set the following to true in your `config/js-localization.php` config file:

```
    'split_export_files' => true,
```

This will in turn _also_ generate the following file(s) in your target directory:
 * `lang-{locale}.js` contains one language's translation strings, if the `split_export_files` config option is set to true

Remember that the files needs to be regenerated using `php artisan js-localization:export` every time any translation strings are edited, added or removed.

Features
--------

You may use Lang.get(), Lang.has(), Lang.choice(), Lang.locale() and trans() (alias for Lang.get()) in your Javascript code. They work just like Laravel's `Lang` facade.
Additionally, you are able to pass configuration properties to your Javascript code as well. There is Config.get() in Javascript, too. Configure which config properties to pass to the client using the `config` field in `config/js-localization.php`. Attention: Do not export any security-critical properties like DB credentials or similar, since they would be visible to anyone using your application!

Variables in messages are supported. For instance: `"This is my test string for :name."`.

Pluralization is also supported, but does not care about the locale. It only uses the English pluralization rule (`"singular text|plural text"`). More complex pluralization quantifiers are not yet supported.


Service providers
-----------------

Assume you are developing a laravel package that depends on this javascript localization features and you want to configure which messages of your package have to be visible to the JS code.

Fortunately that's pretty easy. Just listen to the `JsLocalization.registerMessages` event and use the `JsLocalization\Facades\JsLocalizationHelper::addMessagesToExport()` method. Like so:

```php
<?php

use Illuminate\Support\ServiceProvider;
use JsLocalization\Facades\JsLocalizationHelper;

class MyServiceProvider extends ServiceProvider
{
    /* ... */

    public function register()
    {
        Event::listen('JsLocalization.registerMessages', function()
        {
            JsLocalizationHelper::addMessagesToExport([
                // list the keys of the messages here, similar
                // to the 'messages' array in the config file
            ]);
        });
    }

    /* ... */
}
```


License
-------

This software is released under the MIT license. See [license](https://raw.github.com/andywer/laravel-js-localization/master/LICENSE).
