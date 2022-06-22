define([
    'jquery',
    'Phpro_CookieConsent/js/model/cookie'
], function ($, cookie) {
    return function (options) {
        const consentContentElement = '#modal-consent-content';

        var consentModal = $(consentContentElement).modal({
            type: 'popup',
            responsive: false,
            modalClass: 'cookie-consent-newsletter-modal',
            innerScroll: true,
            clickableOverlay: true,
            buttons: [
                {
                    text: $.mage.__('Allow all'),
                    class: 'action primary consent-btn consent-btn-allow',
                    click: function () {
                        cookie.saveAll(options.cookie_name, options.expiration, options.secure, options.cookie_groups_data.system_names);
                        cookie.closeCookieNotice();
                        consentModal.modal("closeModal");
                    }
                },
                {
                    text: $.mage.__('Save'),
                    class: 'action primary consent-btn consent-btn-save',
                    click: function () {
                        cookie.saveSelected(options.cookie_name, options.expiration, options.secure, options.cookie_groups_data.system_names);
                        cookie.closeCookieNotice();
                        consentModal.modal("closeModal");
                    }
                },
            ]
        });

        init();

        $(document).on('click', '.notice__btn-settings, .btn-cookie-preferences-show', function (event) {
            event.preventDefault();
            consentModal.modal('openModal');
        });

        $(document).on('click', '.consent-tab', function (event) {
            event.preventDefault();
            onClickTab($(this));
        });

        // Fix for modal not closing when clicking overlay on mobile (magento bug)
        $(document).on('click', '.modal-popup.cookie-consent-newsletter-modal', function () {
            consentModal.modal("closeModal");
        });

        $(document).on('click', '.cookie-consent-newsletter-modal .modal-inner-wrap', function (event) {
            event.stopPropagation()
        });

        function init() {
            var consent = cookie.getConsentCookieObject(options.cookie_name);
            onClickTab($('#cookie-policy'));
            if (consent !== undefined) {
                var values = Object.entries(JSON.parse(consent));
                $.each(values, function (index, value) {
                    if ('cg_' === value[0].substring(0, 3)) {
                        var checkboxSelector = '.phpro-cookie-consent-modal .consent-tab-content.' + value[0].substring(3, value[0].length) + ' .cookie-toggle input[type="checkbox"]';
                        $(checkboxSelector).prop('checked', value[1]);
                    }
                });
            }
        }

        function onClickTab(tab) {
            $('.consent-tab').each(function () {
                $(this).removeClass('active');
            });
            tab.addClass('active');
            $('.consent-tab-content').each(function () {
                $(this).css('display', 'none');
            });
            $('.consent-tab-content.' + tab.attr('id')).css('display', 'block');
        }
    }
});
