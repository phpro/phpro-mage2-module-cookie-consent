<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Api\Data;

interface CookieGroupInterface
{
    const ENTITY = 'phpro_cc_cookie_group';
    const ENTITY_TABLE = self::ENTITY . '_entity';

    const FIELD_SYSTEM_NAME = 'system_name';
    const FIELD_NAME = 'name';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_IS_ESSENTIAL = 'is_essential';
    const FIELD_IS_ACTIVE = 'is_active';

    const ATTRIBUTE_GROUP_GENERAL = 'General';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getSystemName(): string;

    /**
     * @param string $systemName
     *
     * @return CookieGroupInterface
     */
    public function setSystemName(string $systemName): CookieGroupInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return CookieGroupInterface
     */
    public function setName(string $name): CookieGroupInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     *
     * @return CookieGroupInterface
     */
    public function setDescription(string $description): CookieGroupInterface;

    /**
     * @return bool
     */
    public function isEssential(): bool;

    /**
     * @param bool $essential
     *
     * @return CookieGroupInterface
     */
    public function setEssential(bool $essential): CookieGroupInterface;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     *
     * @return CookieGroupInterface
     */
    public function setActive(bool $active): CookieGroupInterface;
}
