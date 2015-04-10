laravel-js-localization
=======================
[![Build Status](https://travis-ci.org/andywer/laravel-js-localization.svg?branch=laravel-4.2)](https://travis-ci.org/andywer/laravel-js-localization) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/badges/quality-score.png?b=laravel-4.2)](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/?branch=laravel-4.2) [![Code Coverage](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/badges/coverage.png?b=laravel-4.2)](https://scrutinizer-ci.com/g/andywer/laravel-js-localization/?branch=laravel-4.2) [![Total Downloads](https://poser.pugx.org/andywer/js-localization/downloads.svg)](https://packagist.org/packages/andywer/js-localization)


Simple, ease-to-use and flexible package for the [Laravel](http://laravel.com/) web framework. Allows you to use localized messages of the Laravel webapp (see `app/lang` directory) in your Javascript code. You may easily configure which messages you need to export.

Installation
------------

Add the following line to the `require` section of your Laravel webapp's `composer.json` file:

```javascript
    "require": {
        "andywer/js-localization": "dev-laravel-4.2"
    }
```


Run `composer update` to install the package.


Finally add the following line to the `providers` array of your `app/config/app.php` file:

```php
    'providers' => array(
        /* ... */
        'JsLocalization\JsLocalizationServiceProvider'
    )
```


Configuration
-------------

Run `php artisan config:publish andywer/js-localization` first. This command copies the package's default configuration to `app/config/packages/andywer/js-localization/config.php`.

You may now edit this file to define the messages you need in your Javascript code. Just edit the `messages` array in the config file.

Example (exports all reminder messages):

```php
<?php

return array(
    // Set the locales you use
    'locales' => array('en'),

    // Set the keys of the messages you want to use in javascript
    'messages' => array(
        'reminder' => array(
            'password', 'user', 'token'
        )
    )

    /*
     * in short:
     * 'messages' => array('reminder')
     *
     *
     * you could also use:
     *
     * 'messages' => array(
     *     'reminder.password',
     *     'reminder.user',
     *     'reminder.token'
     * )
     */
);
```

__Important:__

The messages configuration will be cached when the JsLocalizationController is used for the first time. After changing the messages configuration you will need to call __`php artisan js-localization:refresh`__ to refresh that cache.


Usage
-----

You just need to add the neccessary `<script>` tags to your layout. Here is an example blade view:

```html
@include('js-localization::head')
<!doctype html>
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

Features
--------

You may use Lang.get(), Lang.has(), Lang.choice(), Lang.locale() and trans() (alias for Lang.get()) in your Javascript code. They work just like Laravel's `Lang` facade.

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
            JsLocalizationHelper::addMessagesToExport(array(
                // list the keys of the messages here, similar
                // to the 'messages' array in the config file
            ));
        });
    }

    /* ... */
}
```


Laravel 4.0
-----------

The package has been updated to work with Laravel 4.1. Edit your composer.json dependency if you are still using 4.0:

```json
    "require": {
        "andywer/js-localization": "dev-laravel-4.0"
    }
```


License
-------

This software is released under the MIT license. See [license](https://raw.github.com/andywer/laravel-js-localization/master/LICENSE).
