# Content

## Cms Blocks

The following CMS blocks are automatically created on the installation of the module:

* **phpro_cookie_consent_cookie_policy_content**
* **phpro_cookie_consent_privacy_policy_content**

These CMS blocks can be found at **Content > Elements > Blocks** and are used in the preferences popup.

### Cookie Policy

Identifier: **phpro_cookie_consent_cookie_policy_content**

This CMS block displays the description for the cookie policy tab in the preferences popup. This content can be altered 
to your needs.

![cookie-policy](./assets/cookie_policy.png "Cookie Policy")


### Privacy Policy

Identifier: **phpro_cookie_consent_privacy_policy_content**

This CMS block displays the description for the privacy policy tab in the preferences popup. This content can be altered 
to your needs.

![privacy-policy](./assets/privacy_policy.png "Privacy Policy")

## Widgets

### Cookie consent overview

The cookie consent overview widget can be used to display an overview of the active cookie groups and their description.

This widget contains no arguments.

![overview-widget](./assets/overview_widget.png "Overview Widget")

### Cookie preferences button

The cookie preferences button widget can be used to display a button/link which opens the preferences popup. This way the
users are able to change their preferences.

This widget contains 1 argument:

* **Button/Link** - depending on your choice the widget will be shown as a generic link or button.

**Config**
![button-widget-config](./assets/button_widget_config.png "Preferences Button Widget Config")

**Result**
![button-widget-btn](./assets/button_widget_btn.png "Preferences Button Widget Button Config")
![button-widget-link](./assets/button_widget_link.png "Preferences Button Widget Link Config")


## Cms Page

A CMS page containing the [Cookie consent overview widget](#cookie-consent-overview) &
 [Cookie preferences button widget](#cookie-preferences-button) is created on the module installation.
 
url key: **consent-overview**

![consent-overview](./assets/consent_overview.png "Consent Overview CMS Page")



