<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Setup\Patch\Data;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;
use PHPro\CookieConsent\Api\CookieGroupRepositoryInterface;
use PHPro\CookieConsent\Api\Data\CookieGroupInterface;
use PHPro\CookieConsent\Model\CookieGroupFactory;

class DefaultCookieData implements DataPatchInterface
{
    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    public function __construct(
        CookieGroupFactory $cookieGroupFactory,
        CookieGroupRepositoryInterface $cookieGroupRepository,
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    public static function getDependencies()
    {
        return [
            CookieGroupAttribute::class,
        ];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->createDefaultGroups();
        $this->createDefaultPage();
        $this->createDefaultBlocks();
    }

    private function createDefaultGroups(): void
    {
        /** @var CookieGroupInterface $group */
        $essential = $this->cookieGroupFactory->create();
        $essential->setStoreId(Store::DEFAULT_STORE_ID);
        $essential->setSystemName('essential');
        $essential->setName('Essential');
        $essential->setActive(true);
        $essential->setEssential(true);
        $essential->setDescription('Essential cookies are strictly necessary to guarantee the proper functioning of the website.');

        /** @var CookieGroupInterface $group */
        $analytical = $this->cookieGroupFactory->create();
        $analytical->setStoreId(Store::DEFAULT_STORE_ID);
        $analytical->setSystemName('analytical');
        $analytical->setName('Analytical');
        $analytical->setActive(true);
        $analytical->setEssential(false);
        $analytical->setDescription('With this you allow us to collect anonymous data about the use ' .
            'of the website with third party cookies, such as the number of clicks and the behavior of visitors ' .
            'on the website. In addition, we can also perform marketing-related actions via this option.' . PHP_EOL .
            'By activating this cookie, you help us to further improve your experience.');

        /** @var CookieGroupInterface $group */
        $marketing = $this->cookieGroupFactory->create();
        $marketing->setStoreId(Store::DEFAULT_STORE_ID);
        $marketing->setSystemName('marketing');
        $marketing->setName('Marketing');
        $marketing->setActive(true);
        $marketing->setEssential(false);
        $marketing->setDescription('With this you allow us to collect marketing-related data via third party cookies.' . PHP_EOL .
            'By activating this cookie, you help us to further improve your experience');

        $this->cookieGroupRepository->save($essential);
        $this->cookieGroupRepository->save($analytical);
        $this->cookieGroupRepository->save($marketing);
    }

    private function createDefaultPage(): void
    {
        $page = $this->pageFactory->create();
        $page->setStoreId(Store::DEFAULT_STORE_ID);
        $page->setIsActive(true);
        $page->setPageLayout('1column');
        $page->setTitle('Cookie Consent Overview');
        $page->setIdentifier('consent-overview');
        $page->setContentHeading('Cookie Consent Overview');
        $page->setContent('<p>{{widget type="PHPro\CookieConsent\Block\Widget\Overview"}}</p>' . PHP_EOL .
            '<p>{{widget type="PHPro\CookieConsent\Block\Widget\Preferences\Button" preferences_button_type="0"}}</p>');

        $this->pageRepository->save($page);
    }

    private function createDefaultBlocks(): void
    {
        $cookiePolicyBlock = $this->blockFactory->create();
        $cookiePolicyBlock->setStoreId(Store::DEFAULT_STORE_ID);
        $cookiePolicyBlock->setIsActive(true);
        $cookiePolicyBlock->setTitle('Cookie Policy Content');
        $cookiePolicyBlock->setIdentifier('phpro_cookie_consent_cookie_policy_content');
        $cookiePolicyBlock->setContent('<p>To make our website even better, we use functional and analytical cookies. ' .
            'Information from this website and your preferences are stored in these cookies by your browser.</p>');

        $privacyPolicyBlock = $this->blockFactory->create();
        $privacyPolicyBlock->setStoreId(Store::DEFAULT_STORE_ID);
        $privacyPolicyBlock->setIsActive(true);
        $privacyPolicyBlock->setTitle('Privacy Policy Content');
        $privacyPolicyBlock->setIdentifier('phpro_cookie_consent_privacy_policy_content');
        $privacyPolicyBlock->setContent('<p>Read more about the use of cookies on this website in our privacy policy.</p>');

        $this->blockRepository->save($cookiePolicyBlock);
        $this->blockRepository->save($privacyPolicyBlock);
    }
}
