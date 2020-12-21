<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Phpro\CookieConsent\Setup\CookieGroupSetup;
use Phpro\CookieConsent\Setup\CookieGroupSetupFactory;

class CookieGroupAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CookieGroupSetupFactory
     */
    private $cookieGroupSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CookieGroupSetupFactory $cookieGroupSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->cookieGroupSetupFactory = $cookieGroupSetupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        /** @var CookieGroupSetup $cookieGroupSetup */
        $cookieGroupSetup = $this->cookieGroupSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $cookieGroupSetup->installEntities();
    }
}
