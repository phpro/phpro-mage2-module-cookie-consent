<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Api;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Phpro\CookieConsent\Api\Data\CookieGroupInterface;

interface CookieGroupRepositoryInterface
{
    /**
     * @param CookieGroupInterface $cookieGroup
     *
     * @return CookieGroupInterface
     * @throws Exception
     */
    public function save(CookieGroupInterface $cookieGroup): CookieGroupInterface;

    /**
     * @param int $cookieGroupId
     * @param int $storeId
     *
     * @return CookieGroupInterface
     * @throws Exception
     */
    public function getById(int $cookieGroupId, int $storeId): CookieGroupInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultsInterface
     * @throws Exception
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param CookieGroupInterface $cookieGroup
     *
     * @return bool true on success
     * @throws Exception
     */
    public function delete(CookieGroupInterface $cookieGroup): bool;

    /**
     * @param int $id
     *
     * @return bool true on success
     * @throws Exception
     */
    public function deleteById(int $id): bool;
}
