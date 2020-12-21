<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Phpro\CookieConsent\Api\CookieGroupRepositoryInterface;
use Phpro\CookieConsent\Api\Data\CookieGroupInterface;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup as CookieGroupResourceModel;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup\Collection;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup\CollectionFactory;

class CookieGroupRepository implements CookieGroupRepositoryInterface
{
    /**
     * @var CookieGroupResourceModel
     */
    private $resourceModel;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        CookieGroupResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(CookieGroupInterface $cookieGroup): CookieGroupInterface
    {
        try {
            $this->resourceModel->save($cookieGroup);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save Cookie Group'), $e);
        }

        return $this->getById((int) $cookieGroup->getId());
    }

    public function getById(int $cookieGroupId, int $storeId = null): CookieGroupInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        if ($storeId) {
            $collection->setStoreId($storeId);
        }

        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', ['eq' => $cookieGroupId]);
        $collection->load();

        $items = $collection->getItems();
        if (count($items) <= 0) {
            throw new NoSuchEntityException(__('Requested Cookie Group entity with entity_id "%1" doesn\'t exist.', $cookieGroupId));
        }

        return current($items);
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(CookieGroupInterface $cookieGroup): bool
    {
        try {
            $this->resourceModel->delete($cookieGroup);
        } catch (Exception $e) {
            throw new StateException(__('Unable to remove Cookie Group %1', $cookieGroup->getId()));
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
