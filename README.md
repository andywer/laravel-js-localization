laravel-js-localization
=======================

Simple, ease-to-use and flexible package for the [Laravel](http://laravel.com/) web framework. Allows you to use localized messages of the Laravel webapp (see `app/lang` directory) in your Javascript code. You may easily configure which messages you need to export.

Installation
------------

Add the following line to the `require` section of your Laravel webapp's `composer.json` file:

```javascript
"require": {
    "andywer/js-localization": "dev-master"
}
```

Run `composer update` to finally install the package.


Configuration
-------------

Run `php artisan config:publish andywer/js-localization` first. This command copies the package's default configuration to `app/config/packages/andywer/js-localization/config.php`.

You may now edit this file to define the messages you need in your Javascript code. Just edit the `messages` array in the config file.

Example (exports all reminder messages):

```php
<?php

return array(
    'messages' => array(
        'reminder' => array(
            'password', 'user', 'token'
        )
    )

    /* you could also use:
     *
     * 'messages' => array(
     *     'reminder.password',
     *     'reminder.user',
     *     'reminder.token'
     * )
     */
);
```


Usage
-----

You just need to add the neccessary `<script>` tags to your view's `<head>`. Here is an example blade view:

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

Pluralization is also supported, but does not care about the local. It only uses the English pluralization rule (`"singular text|plural text"`). More complex pluralization quantifiers are not yet supported.


License
-------

This software is released under the MIT license. See [license](https://raw.github.com/andywer/laravel-js-localization/master/LICENSE).
