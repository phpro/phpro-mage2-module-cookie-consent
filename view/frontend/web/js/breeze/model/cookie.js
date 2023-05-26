/* global _ */
(function () {
    'use strict';

    $.widget('phpprocookie', {
        options: {
        },

        consentCookieExists: function (name) {
            return ($.cookies.get(name));
        },

        getConsentCookieObject: function(name) {
            return $.cookies.get(name);
        },

        saveAll: function (name, expiration, secure, systemNames) {
            var data = {};
            $.each(systemNames, function (index, value) {
                var sysName = 'cg_' + value;
                data[sysName] = 1;
                var checkboxSelector = '.phpro-cookie-consent-modal .consent-tab-content.' + value + ' .cookie-toggle input';
                $(checkboxSelector).prop('checked', data[sysName]);
            });
            data['expire'] = expiration;
            data['secure'] = secure;
            $.cookies.set(name, JSON.stringify(data), {
                expires: this.getExpiration(expiration)
            });
        },

        saveSelected: function (name, expiration, secure, systemNames) {
            var data = {};
            $.each(systemNames, function (index, value) {
                var sysName = 'cg_' + value;
                var checkboxSelector = '.phpro-cookie-consent-modal .consent-tab-content.' + value + ' .cookie-toggle input[type="checkbox"]';
                data[sysName] = ($(checkboxSelector).is(':checked') ? 1 : 0);
            });
            data['expire'] = expiration;
            data['secure'] = secure;
            $.cookies.set(name, JSON.stringify(data), {
                expires: this.getExpiration(expiration)
            });
        },

        closeCookieNotice: function () {
            $('.phpro-cookie-notice').css('display', 'none');
        },

        getExpiration: function (expiration) {
            var today = new Date();
            var expireDate = new Date(today);
            expireDate.setDate(today.getDate()+expiration);

            return expireDate;
        }
    });

    $.mage = $.mage || {};
    $.mage.phpprocookie = $.fn.phpprocookie;

})();
