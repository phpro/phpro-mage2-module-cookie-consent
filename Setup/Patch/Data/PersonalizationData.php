<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;
use Phpro\CookieConsent\Api\CookieGroupRepositoryInterface;
use Phpro\CookieConsent\Api\Data\CookieGroupInterface;
use Phpro\CookieConsent\Model\CookieGroupFactory;

class PersonalizationData implements DataPatchInterface
{
    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;


    public function __construct(
        CookieGroupFactory $cookieGroupFactory,
        CookieGroupRepositoryInterface $cookieGroupRepository
    ) {
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    public static function getDependencies()
    {
        return [
            CookieGroupAttribute::class,
            DefaultCookieData::class,
        ];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->createDefaultGroups();
    }

    private function createDefaultGroups(): void
    {
        /** @var CookieGroupInterface $group */
        $personalization = $this->cookieGroupFactory->create();
        $personalization->setStoreId(Store::DEFAULT_STORE_ID);
        $personalization->setSystemName('personalization');
        $personalization->setName('Personalization');
        $personalization->setActive(true);
        $personalization->setEssential(false);
        $personalization->setDescription('Personalization cookies are used for ad personalization and remarketing.');

        $this->cookieGroupRepository->save($personalization);
    }
}
