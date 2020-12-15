<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Api\Data;

interface EavModelInterface
{
    const FIELD_STORE_ID = 'store_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     *
     * @return EavModelInterface
     */
    public function setStoreId(int $storeId): EavModelInterface;

    /**
     * @return string
     */
    public function getEntityType(): string;
}
