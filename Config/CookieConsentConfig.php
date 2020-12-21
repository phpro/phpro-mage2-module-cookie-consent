<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class CookieConsentConfig
{
    const CONFIG_PATH_COOKIE_NAME = 'phpro_cookie_consent/general/cookie_name_preferences';
    const CONFIG_PATH_COOKIE_EXPIRATION = 'phpro_cookie_consent/general/cookie_expiration';
    const CONFIG_PATH_ENABLED = 'phpro_cookie_consent/general/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagement;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagement
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagement = $storeManagement;
    }

    public function getCookieName(): string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_COOKIE_NAME,
            ScopeInterface::SCOPE_STORE,
            $this->storeManagement->getStore()->getId()
        );
    }

    public function getExpirationDays(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_COOKIE_EXPIRATION,
            ScopeInterface::SCOPE_STORE,
            $this->storeManagement->getStore()->getId()
        );
    }

    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManagement->getStore()->getId()
        );
    }
}
