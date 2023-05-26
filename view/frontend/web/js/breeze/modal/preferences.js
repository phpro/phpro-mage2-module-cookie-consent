/* global _ */
(function () {//todo fixes here, this file makes issue with notice bar too
    'use strict';

    $.view('modal-consent-content', {
        component: 'Phpro_CookieConsent/js/modal/preferences',
        options: {
            default: 'value'
        },

        create: function () {
            let options = this.options;
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
                            $.mage.phpprocookie().saveAll(options.cookie_name, options.expiration, options.secure, options.cookie_groups_data.system_names);
                            $.mage.phpprocookie().closeCookieNotice();
                            consentModal.modal("closeModal");
                        }
                    },
                    {
                        text: $.mage.__('Save'),
                        class: 'action primary consent-btn consent-btn-save',
                        click: function () {
                            $.mage.phpprocookie().saveSelected(options.cookie_name, options.expiration, options.secure, options.cookie_groups_data.system_names);
                            $.mage.phpprocookie().closeCookieNotice();
                            consentModal.modal("closeModal");
                        }
                    },
                ]
            });

            var consent = $.mage.phpprocookie().getConsentCookieObject(options.cookie_name);
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

            $(document).on('click', '.notice__btn-settings, .btn-cookie-preferences-show', function (event) {
                event.preventDefault();
                consentModal.modal('openModal');
            });

            // Fix for modal not closing when clicking overlay on mobile (magento bug)
            $(document).on('click', '.modal-popup.cookie-consent-newsletter-modal', function () {
                consentModal.modal("closeModal");
            });

            $(document).on('click', '.cookie-consent-newsletter-modal .modal-inner-wrap', function (event) {
                event.stopPropagation()
            });

            $(document).on('click', '.consent-tab', function (event) {
                event.preventDefault();
                onClickTab($(this));
            });

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
})();
