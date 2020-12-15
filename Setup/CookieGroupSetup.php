<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Setup;

use Magento\Eav\Setup\EavSetup;
use PHPro\CookieConsent\Api\Data\CookieGroupInterface;
use PHPro\CookieConsent\Model\CookieGroupAttribute;
use PHPro\CookieConsent\Model\ResourceModel\CookieGroup as CookieGroupResourceModel;

class CookieGroupSetup extends EavSetup
{
    public function getDefaultEntities()
    {
        $entity = CookieGroupInterface::ENTITY;

        $attributes = [
            CookieGroupInterface::FIELD_SYSTEM_NAME => [
                'type' => 'static',
                'group' => CookieGroupInterface::ATTRIBUTE_GROUP_GENERAL,
                'label' => 'System name',
                'input' => 'text',
                'sort_order' => 5,
                'required' => true,
            ],
            CookieGroupInterface::FIELD_NAME => [
                'type' => 'varchar',
                'group' => CookieGroupInterface::ATTRIBUTE_GROUP_GENERAL,
                'label' => 'Name',
                'input' => 'text',
                'sort_order' => 10,
                'required' => true,
            ],
            CookieGroupInterface::FIELD_DESCRIPTION => [
                'type' => 'text',
                'group' => CookieGroupInterface::ATTRIBUTE_GROUP_GENERAL,
                'label' => 'Description',
                'input' => 'textarea',
                'sort_order' => 15,
                'required' => false,
            ],
            CookieGroupInterface::FIELD_IS_ESSENTIAL => [
                'type' => 'static',
                'group' => CookieGroupInterface::ATTRIBUTE_GROUP_GENERAL,
                'label' => 'Essential',
                'input' => 'boolean',
                'sort_order' => 20,
                'required' => false,
            ],
            CookieGroupInterface::FIELD_IS_ACTIVE => [
                'type' => 'static',
                'group' => CookieGroupInterface::ATTRIBUTE_GROUP_GENERAL,
                'label' => 'Is Active',
                'input' => 'boolean',
                'sort_order' => 25,
                'required' => false,
            ],
        ];

        $entities = [
            $entity => [
                'entity_model' => CookieGroupResourceModel::class,
                'attribute_model' => CookieGroupAttribute::class,
                'table' => CookieGroupInterface::ENTITY_TABLE,
                'attributes' => $attributes,
            ],
        ];

        return $entities;
    }
}
