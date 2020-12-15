<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Model;

use Magento\Framework\Model\AbstractModel;
use PHPro\CookieConsent\Api\Data\CookieGroupInterface;
use PHPro\CookieConsent\Api\Data\EavModelInterface;

class CookieGroup extends AbstractModel implements CookieGroupInterface, EavModelInterface
{
    protected $_eventPrefix = self::ENTITY;

    public function _construct()
    {
        $this->_init(ResourceModel\CookieGroup::class);
    }

    public function getId(): int
    {
        return (int) parent::getId();
    }

    public function getSystemName(): string
    {
        return (string) $this->getData(self::FIELD_SYSTEM_NAME);
    }

    public function setSystemName(string $systemName): CookieGroupInterface
    {
        $this->setData(self::FIELD_SYSTEM_NAME, $systemName);

        return $this;
    }

    public function getName(): string
    {
        return (string) $this->getData(self::FIELD_NAME);
    }

    public function setName(string $name): CookieGroupInterface
    {
        $this->setData(self::FIELD_NAME, $name);

        return $this;
    }

    public function getDescription(): string
    {
        return (string) $this->getData(self::FIELD_DESCRIPTION);
    }

    public function setDescription(string $description): CookieGroupInterface
    {
        $this->setData(self::FIELD_DESCRIPTION, $description);

        return $this;
    }

    public function isEssential(): bool
    {
        return (bool) $this->getData(self::FIELD_IS_ESSENTIAL);
    }

    public function setEssential(bool $essential): CookieGroupInterface
    {
        $this->setData(self::FIELD_IS_ESSENTIAL, $essential);

        return $this;
    }

    public function isActive(): bool
    {
        return (bool) $this->getData(self::FIELD_IS_ACTIVE);
    }

    public function setActive(bool $active): CookieGroupInterface
    {
        $this->setData(self::FIELD_IS_ACTIVE, $active);

        return $this;
    }

    public function getStoreId(): int
    {
        return (int) $this->getData(self::FIELD_STORE_ID);
    }

    public function setStoreId(int $storeId): EavModelInterface
    {
        $this->setData(self::FIELD_STORE_ID, $storeId);

        return $this;
    }

    public function getEntityType(): string
    {
        return self::ENTITY;
    }

    public function getDefaultAttributeSetId(): int
    {
        return (int) $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }
}
