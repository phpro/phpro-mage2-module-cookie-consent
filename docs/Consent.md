# Consent Mode V2

## Configuration
### Enable the Google Consent feature
**Stores > Settings > Configuration > General > Cookie Consent -> Google Consent**

### Remarks
- Make sure you are using the default system names of the cookie groups:
    - essential
    - analytical
    - marketing
    - personalization
- Make sure your Google Tag Manager instance is injected AFTER the 'phpro_cookie_gtag' block. It's important to have the consent first be set to 'DENIED' before loading Google Tag Manager.




