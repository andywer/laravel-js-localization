(function() {
    var locale;
    var messages = {};


    /* Utility functions: */

    /**
     * Replace variables used in the message by appropriate values.
     *
     * @param string message        Input message.
     * @param object replacements   Associative array: { variableName: "replacement", ... }
     * @return string The input message with all replacements applied.
     */
    var applyReplacements = function (message, replacements) {
        for (var replacementName in replacements) {
            var replacement = replacements[replacementName];

            var regex = new RegExp(':'+replacementName, 'g');
            message = message.replace(regex, replacement);
        }

        return message;
    };


    /* Lang object: */
    /* (works like the Laravel Lang object) */

    var Lang = {
        get : function(messageKey, replacements) {
            if (typeof messages[messageKey] == "undefined") {
                return messageKey;
            }

            var message = messages[messageKey];

            if (replacements) {
                message = applyReplacements(message, replacements);
            }

            return message;
        },

        has : function(messageKey) {
            return typeof messages[messageKey] != "undefined";
        },

        choice : function(messageKey, count, replacements) {
            if (typeof messages[messageKey] == "undefined") {
                return messageKey;
            }

            var message;
            var messageSplitted = messages[messageKey].split('|');

            if (count == 1) {
                message = messageSplitted[0];
            } else {
                message = messageSplitted[1];
            }

            if (replacements) {
                message = applyReplacements(message, replacements);
            }

            return message;
        },

        setLocale : function(localeId) {
            locale = localeId;
        },

        locale : function() {
            return locale;
        },

        /**
         * Used to initialize the message catalog. You may use this
         * method to add further messages on runtime if neccessary.
         *
         * @param object _messages  An associative array: { messageKey: "message", ... }
         * @return void
         */
        addMessages : function(_messages) {
            for (var key in _messages) {
                messages[key] = _messages[key];
            }
        }
    };


    /* Export: */

    this.Lang = Lang;
    this.trans = Lang.get;
})();