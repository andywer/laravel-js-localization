(function() {
    var locale;
    var messages;

    var Lang = {
        get : function(messageId) {
            if (typeof messages[messageId] == "undefined") {
                return messageId;
            } else {
                return messages[messageId];
            }
        },

        has : function(messageId) {
            return typeof messages[messageId] != "undefined";
        },

        setLocale : function(localeId) {
            locale = localeId;
        },

        locale : function() {
            return locale;
        },

        addMessages : function(_messages) {
            for (var key in _messages) {
                messages[key] = _messages[key];
            }
        }
    };

    this.Lang = Lang;
    this.trans = Lang.get;
})();