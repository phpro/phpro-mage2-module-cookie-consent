<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\UniqueValidationInterface;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Phpro\CookieConsent\Model\CookieGroupFactory;

abstract class AbstractResource extends AbstractEntity
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CookieGroupFactory $cookieGroupFactory,
        $data = [],
        UniqueValidationInterface $uniqueValidator = null
    ) {
        parent::__construct($context, $data, $uniqueValidator);
        $this->storeManager = $storeManager;
        $this->cookieGroupFactory = $cookieGroupFactory;
    }

    public function getDefaultStoreId()
    {
        return Store::DEFAULT_STORE_ID;
    }

    protected function _getLoadAttributesSelect($object, $table)
    {
        if ($this->storeManager->hasSingleStore()) {
            $storeId = (int) $this->storeManager->getStore(true)->getId();
        } else {
            $storeId = (int) $object->getStoreId();
        }

        $setId = $object->getDefaultAttributeSetId();
        $storeIds = [$this->getDefaultStoreId()];
        if ($storeId !== $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['attr_table' => $table], [])
            ->where("attr_table.{$this->getLinkField()} = ?", $object->getData($this->getLinkField()))
            ->where('attr_table.store_id IN (?)', $storeIds);

        if ($setId) {
            $select->join(
                ['set_table' => $this->getTable('eav_entity_attribute')],
                $this->getConnection()->quoteInto(
                    'attr_table.attribute_id = set_table.attribute_id' . ' AND set_table.attribute_set_id = ?',
                    $setId
                ),
                []
            );
        }

        return $select;
    }

    protected function _prepareLoadSelect(array $selects)
    {
        $select = parent::_prepareLoadSelect($selects);
        $select->order('store_id');

        return $select;
    }

    protected function _saveAttributeValue($object, $attribute, $value)
    {
        $connection = $this->getConnection();
        $hasSingleStore = $this->storeManager->hasSingleStore();
        $storeId = $hasSingleStore
            ? $this->getDefaultStoreId()
            : (int) $this->storeManager->getStore($object->getStoreId())->getId();
        $table = $attribute->getBackend()->getTable();

        $entityIdField = $this->getLinkField();
        $conditions = [
            'attribute_id = ?' => $attribute->getAttributeId(),
            "{$entityIdField} = ?" => $object->getData($entityIdField),
            'store_id <> ?' => $storeId,
        ];
        if ($hasSingleStore
            && !$object->isObjectNew()
            && $this->isAttributePresentForNonDefaultStore($attribute, $conditions)
        ) {
            $connection->delete(
                $table,
                $conditions
            );
        }

        $data = new DataObject(
            [
                'attribute_id' => $attribute->getAttributeId(),
                'store_id' => $storeId,
                $entityIdField => $object->getData($entityIdField),
                'value' => $this->_prepareValueForSave($value, $attribute),
            ]
        );
        $bind = $this->_prepareDataForTable($data, $table);

        $this->_attributeValuesToSave[$table][] = $bind;

        return $this;
    }

    protected function _insertAttribute($object, $attribute, $value)
    {
        $storeId = (int) $this->storeManager->getStore($object->getStoreId())->getId();
        if ($this->getDefaultStoreId() !== $storeId) {
            $table = $attribute->getBackend()->getTable();

            $select = $this->getConnection()->select()
                ->from($table)
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', $this->getDefaultStoreId())
                ->where($this->getLinkField() . ' = ?', $object->getData($this->getLinkField()));
            $row = $this->getConnection()->fetchOne($select);

            if (!$row) {
                $data = new DataObject(
                    [
                        'attribute_id' => $attribute->getAttributeId(),
                        'store_id' => $this->getDefaultStoreId(),
                        $this->getLinkField() => $object->getData($this->getLinkField()),
                        'value' => $this->_prepareValueForSave($value, $attribute),
                    ]
                );
                $bind = $this->_prepareDataForTable($data, $table);
                $this->getConnection()->insertOnDuplicate($table, $bind, ['value']);
            }
        }

        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $connection = $this->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $this->getLinkField();
        $select = $connection->select()
            ->from($table, 'value_id')
            ->where("$entityIdField = :entity_field_id")
            ->where('store_id = :store_id')
            ->where('attribute_id = :attribute_id');
        $bind = [
            'entity_field_id' => $object->getId(),
            'store_id' => $storeId,
            'attribute_id' => $attribute->getId(),
        ];
        $valueId = $connection->fetchOne($select, $bind);
        /*
         * When value for store exist
         */
        if ($valueId) {
            $bind = ['value' => $this->_prepareValueForSave($value, $attribute)];
            $where = ['value_id = ?' => (int) $valueId];

            $connection->update($table, $bind, $where);
        } else {
            $bind = [
                $entityIdField => (int) $object->getId(),
                'attribute_id' => (int) $attribute->getId(),
                'value' => $this->_prepareValueForSave($value, $attribute),
                'store_id' => (int) $storeId,
            ];

            $connection->insert($table, $bind);
        }

        return $this;
    }

    private function isAttributePresentForNonDefaultStore($attribute, $conditions)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($attribute->getBackend()->getTable());
        foreach ($conditions as $condition => $conditionValue) {
            $select->where($condition, $conditionValue);
        }
        $select->limit(1);

        return !empty($connection->fetchRow($select));
    }
}
