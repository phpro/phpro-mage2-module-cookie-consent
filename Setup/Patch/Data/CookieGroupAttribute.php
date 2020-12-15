<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use PHPro\CookieConsent\Setup\CookieGroupSetup;
use PHPro\CookieConsent\Setup\CookieGroupSetupFactory;

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
