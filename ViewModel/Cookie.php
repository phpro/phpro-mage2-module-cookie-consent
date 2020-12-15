<?php declare(strict_types=1);

namespace PHPro\CookieConsent\ViewModel;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPro\CookieConsent\Api\Data\CookieGroupInterface;
use PHPro\CookieConsent\Config\CookieConsentConfig;
use PHPro\CookieConsent\Model\ResourceModel\CookieGroup\CollectionFactory;

class Cookie implements ArgumentInterface
{
    const IDENTIFIER_COOKIE_POLICY_CONTENT = 'phpro_cookie_consent_cookie_policy_content';
    const IDENTIFIER_PRIVACY_POLICY_CONTENT = 'phpro_cookie_consent_privacy_policy_content';

    /**
     * @var CookieManagerInterface
     */
    private $cookieManagement;

    /**
     * @var CookieConsentConfig
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagement;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $groups;

    public function __construct(
        CookieManagerInterface $cookieManagement,
        CookieConsentConfig $cookieConsentConfig,
        StoreManagerInterface $storeManagement,
        CollectionFactory $collectionFactory
    ) {
        $this->cookieManagement = $cookieManagement;
        $this->config = $cookieConsentConfig;
        $this->storeManagement = $storeManagement;
        $this->collectionFactory = $collectionFactory;
    }

    public function getCookieGroups(): array
    {
        if (!$this->groups) {
            $collection = $this->collectionFactory->create();
            $collection->setStoreId($this->storeManagement->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_active', 1);
            $this->groups = $collection->getItems();
        }

        return $this->groups;
    }

    public function getGroupsData(): string
    {
        $data = [];
        $groups = $this->getCookieGroups();

        /** @var CookieGroupInterface $group */
        foreach ($groups as $group) {
            $data[] = $group->getSystemName();
        }
        $result = [
            'system_names' => $data,
        ];

        return json_encode($result);
    }

    public function getCookieName(): string
    {
        return $this->config->getCookieName();
    }

    public function getExpirationDays(): int
    {
        return $this->config->getExpirationDays();
    }

    public function isFrontUrlSecure(): int
    {
        return (int) $this->storeManagement->getStore()->isFrontUrlSecure();
    }

    public function getCookiePolicyIdentifier(): string
    {
        return self::IDENTIFIER_COOKIE_POLICY_CONTENT;
    }

    public function getPrivacyPolicyIdentifier(): string
    {
        return self::IDENTIFIER_PRIVACY_POLICY_CONTENT;
    }
}
