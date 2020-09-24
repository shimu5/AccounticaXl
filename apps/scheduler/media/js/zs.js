(function($, undefined) {

    //$.tools = $.tools || {version: '@VERSION'};
    ZS = {
        'daterange': function(from, to, options) {
            options = $.extend({}, options, { trigger: true, format: 'yyyy-mm-dd' });
            from = from || $("from_date");
            to = to || $("to_date");

            from.dateinput(options);
            to.dateinput(options);

            from.data("dateinput").change(function() {
            	// we use it's value for the seconds input min option
            	to.data("dateinput").setMin(this.getValue(), true);
            });
            to.data("dateinput").setMin(from.data("dateinput").getValue(), true);
        }
    };

    ZS.Config = {
        _values: {

        },
        get: function(name, default_value) {
            if (name in this._values)
                return this._values[name];
            return default_value;
        },
        set: function(name, value) {
            this._values[name] = value
        }
    };

    ZS.Config.set('Currency', 'TK');

    ZS.BaseDialog = {

        _action_handlers: {},
        displayMessage: function(element, e) {

            if ($.isFunction(this.options.message))
                message = this.options.message.call(this, element, e);
            else
                message = this.options.message;

            if (ZS.Dialog.debug || ZS.debug) { console.log('displaying message "', message, '"'); }

            if (confirm(message) == false)
                return false;
        },

        handleAction: function(element, e) {
            if (ZS.Dialog.debug || ZS.debug) { console.log('handling action'); }
            var actionHandler = this.options.action;
            if (actionHandler == null ) return true;

            if (actionHandler in ZS.BaseDialog._action_handlers)
                return ZS.BaseDialog._action_handlers[actionHandler].call(this, element, e);

            console.log('action handler', actionHandler, 'not found in registered action handlers', ZS.BaseDialog._action_handlers);

        },

        registerActionHandler: function(name, fn) {
            ZS.BaseDialog._action_handlers[name] = fn;
        },

        handler: function(element, e) {
            if (this.displayMessage(element, e) == false) return false;
            if (this.handleAction(element, e) == false) return false;
            //console.log(this, element, event);
            //return false;
            //if (this.displayMessage() == false) return false;
            //if (this.handleAction() == false) return false;
        }

    };

    ZS.Dialog = function() {

    };

    ZS.Dialog.prototype = ZS.BaseDialog;

    ZS.Dialog.confirm = function(options) {
        options = $.extend({}, ZS.Dialog.default_options, options);

        if (ZS.Dialog.debug || ZS.debug) { console.log('Initializing ', $(this), ' event with ', options); }

        var instance = new ZS.Dialog();
        instance.options = options;

        if (ZS.Dialog.debug || ZS.debug) { console.log('Instance created ', instance); }
        if (ZS.Dialog.debug || ZS.debug) { console.log('registering', options.event, 'event'); }
        $(this).bind(options.event, function(e) {
            if (ZS.Dialog.debug || ZS.debug) { console.log(options.event, 'event handled'); }
            return instance.handler(this, e);
        });

    };

    ZS.debug = 0;

    ZS.Dialog.default_options = {
        event : 'click',
        action: null
    };

    ZS.BaseDialog.registerActionHandler('AjaxPostReload', function(element, e) {
        $.post($(element).attr('href'), function(data) {
            //ZS.flash(data);
            window.location.reload();
        });
        return false;
    });

})(jQuery);