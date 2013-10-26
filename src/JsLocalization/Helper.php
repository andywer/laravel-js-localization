<?php
namespace JsLocalization;

use App;
use Event;
use Illuminate\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class Helper
{
    
    /**
     * Array of message keys. A set of messages that are
     * supposed to be exported to the JS code in addition
     * to Config::get('js-localization::config.messages').
     *
     * @var array
     */
    protected $messagesToExport = array();

    /**
     * Allows registration of additional messages to
     * export to the JS code. The additional messages
     * registered using this method extend the
     * Config::get('js-localization::config.messages')
     * array.
     * Don't forget to run `php artisan js-localization:refresh`!
     *
     * @param array $messageKeys    Array of message keys.
     * @return void
     */
    public function addMessagesToExport (array $messageKeys)
    {
        $this->messagesToExport = array_unique(
            array_merge(
                $this->messagesToExport,
                $this->resolveMessageKeyArray($messageKeys)
            )
        );
    }

    /**
     * Similar to addMessagesToExport(), but does not
     * register an array of message keys, but the
     * messages of a whole language file (one of the
     * PHP files in app/lang).
     *
     * @param string $filePath  Path to the message file.
     * @param string $prefix    Optional. Prefix to prepend before the message keys.
     * @return void
     */
    public function addMessageFileToExport ($filePath, $prefix="")
    {
        if (!File::isFile($filePath)) {
            throw new FileNotFoundException("File not found: $filePath");
        }

        $messages = require_once $filePath;
        $prefix  = $this->prefix($prefix);
        $prefix .= preg_replace('/\.php$/i', '', basename($filePath)) . '.';

        $this->messagesToExport = array_unique(
            array_merge(
                $this->messagesToExport,
                $this->resolveMessageArrayToMessageKeys($messages, $prefix)
            )
        );
    }

    /**
     * Returns the message keys previously registered
     * by addMessagesToExport(). Nested arrays have
     * already been resolved to a single flat array.
     *
     * @return array
     *      Array of message keys to export to the JS code.
     */
    public function getAdditionalMessages ()
    {
        return $this->messagesToExport;
    }

    /**
     * Trigger registerMessages event.
     * Other Laravel packages that use this package
     * and need to export their own messages to
     * the JS code should use a listener on that event.
     *
     * @return void
     */
    public function triggerRegisterMessages ()
    {
        Event::fire('JsLocalization.registerMessages');
    }

    /**
     * Takes an array of message keys with nested
     * sub-arrays and returns a flat array of
     * fully qualified message keys.
     *
     * @param array $messageKeys    Complex array of message keys.
     * @return array Flat array of fully qualified message keys.
     */
    public function resolveMessageKeyArray (array $messageKeys)
    {
        $flatArray = array();

        foreach ($messageKeys as $index=>$key) {
            $this->resolveMessageKey($key, $index, function($qualifiedKey) use(&$flatArray)
                {
                    $flatArray[] = $qualifiedKey;
                });
        }

        return $flatArray;
    }

    /**
     * Resolves a message array with nested
     * sub-arrays to a flat array of fully
     * qualified message keys.
     *
     * @param array $messages   Complex message array (like the ones in the app/lang/* files).
     * @return array Flat array of fully qualified message keys.
     */
    public function resolveMessageArrayToMessageKeys (array $messages, $prefix="")
    {
        $flatArray = array();

        foreach ($messages as $key=>$message) {
            $this->resolveMessageToKeys($message, $key, function($qualifiedKey) use(&$flatArray)
                {
                    $flatArray[] = $qualifiedKey;
                }, $prefix);
        }

        return $flatArray;
    }

    /**
     * Returns the concatenation of prefix and key if the key
     * is a string. If the key is an array then the function
     * will recurse.
     *
     * @param mixed $key            An array item read from the configuration ('messages' array).
     * @param mixed $keyIndex       The array index of $key. Is neccessary if $key is an array.
     * @param callable $callback    A callback function: function($fullyQualifiedKey).
     * @param string $prefix        Optional key prefix.
     */
    private function resolveMessageKey ($key, $keyIndex, $callback, $prefix="")
    {
        if (is_array($key)) {
            $_prefix = $prefix ? $prefix.$keyIndex."." : $keyIndex.".";

            foreach ($key as $_index=>$_key) {
                $this->resolveMessageKey($_key, $_index, $callback, $_prefix);
            }

        } else {
            $callback($prefix.$key);
        }
    }

    /**
     * Returns the concatenation of prefix and key if the value
     * is a message. If the value is an array then the function
     * will recurse.
     *
     * @param mixed $message        An array item read from a message file array.
     * @param mixed $key            The array key of $message.
     * @param callable $callback    A callback function: function($fullyQualifiedKey).
     * @param string $prefix        Optional key prefix.
     */
    private function resolveMessageToKeys ($message, $key, $callback, $prefix="")
    {
        if (is_array($message)) {
            $_prefix = $prefix ? $prefix.$key."." : $key.".";

            foreach ($message as $_key=>$_message) {
                $this->resolveMessageToKeys($_message, $_key, $callback, $_prefix);
            }

        } else {
            $callback($prefix.$key);
        }
    }

    /**
     * Appends a dot to the prefix if neccessary.
     *
     * @param string $prefix    Prefix to validate and possibly append dot to.
     * @return string Processed prefix.
     */
    private function prefix ($prefix)
    {
        if ($prefix) {
            $prefixLastChar = substr($prefix, -1);

            if ($prefixLastChar != '.' && $prefixLastChar != ':') {
                $prefix .= '.';
            }
        }

        return $prefix;
    }

}