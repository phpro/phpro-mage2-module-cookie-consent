<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Helper;

use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class AttributesHelper
{
    /**
     * @var AttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        AttributeRepositoryInterface $attributeRepository,
        AttributeCollectionFactory $attributeCollectionFactory,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getAttributes(string $entityTypeCode, int $attributeSetId): array
    {
        /** @var AttributeGroupInterface[] $groups */
        $groups = $this->getGroupsForAttributeSet($attributeSetId);

        $attributes = [];
        $groupIds = [];

        foreach ($groups as $group) {
            $groupIds[$group->getAttributeGroupId()] = $group->getAttributeGroupCode();
            $attributes[$group->getAttributeGroupCode()] = [];
        }

        /** @var AttributeCollection $collection */
        $collection = $this->attributeCollectionFactory->create();
        $collection->setAttributeGroupFilter(array_keys($groupIds));

        $mapAttributeToGroup = [];

        foreach ($collection->getItems() as $attribute) {
            $mapAttributeToGroup[$attribute->getAttributeId()] = $attribute->getAttributeGroupId();
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setAscendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AttributeGroupInterface::GROUP_ID, array_keys($groupIds), 'in')
            ->addSortOrder($sortOrder)
            ->create();

        $groupAttributes = $this->attributeRepository->getList($entityTypeCode, $searchCriteria)->getItems();

        foreach ($groupAttributes as $attribute) {
            $attributeGroupId = $mapAttributeToGroup[$attribute->getAttributeId()];
            $attributeGroupCode = $groupIds[$attributeGroupId];
            $attributes[$attributeGroupCode][] = $attribute;
        }

        return $attributes;
    }

    private function getGroupsForAttributeSet(int $attributeSetId)
    {
        $attributeGroups = [];

        $this->searchCriteriaBuilder->addFilter(
            AttributeGroupInterface::ATTRIBUTE_SET_ID,
            $attributeSetId
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $attributeGroupSearchResult = $this->attributeGroupRepository->getList($searchCriteria);

        foreach ($attributeGroupSearchResult->getItems() as $group) {
            $attributeGroups[$group->getAttributeGroupCode()] = $group;
        }

        return $attributeGroups;
    }
}
