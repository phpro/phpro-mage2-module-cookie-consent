<?php
declare(strict_types=1);

namespace Phpro\CookieConsent\Ui\DataProvider\CookieGroup\Form\Modifier;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Phpro\CookieConsent\Helper\AttributesHelper;
use Phpro\CookieConsent\Helper\UiComponentHelper;
use Phpro\CookieConsent\Model\CookieGroup;
use Phpro\CookieConsent\Model\CookieGroupFactory;

/**
 * Class Fields
 */
class Fields implements ModifierInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AttributesHelper
     */
    private $attributesHelper;

    /**
     * @var UiComponentHelper
     */
    private $uiComponentHelper;

    private $resourceConnection;

    public function __construct(
        Registry $registry,
        CookieGroupFactory $cookieGroupFactory,
        UrlInterface $urlBuilder,
        AttributesHelper $attributesHelper,
        UiComponentHelper $uiComponentHelper,
        ResourceConnection $resourceConnection
    ) {
        $this->registry = $registry;
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->urlBuilder = $urlBuilder;
        $this->attributesHelper = $attributesHelper;
        $this->uiComponentHelper = $uiComponentHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     *
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        $model = $this->getModel();
        $parameters = [
            'id' => $model->getId(),
            'store' => $model->getStoreId(),
        ];

        $submitUrl = $this->urlBuilder->getUrl('phpro_cookie_consent/cookiegroup/save', $parameters);

        return array_replace_recursive(
            $data,
            [
                'config' => [
                    'submit_url' => $submitUrl,
                    'data' => $model->getData(),
                ],
            ]
        );
    }

    public function modifyMeta(array $meta)
    {
        $model = $this->getModel();

        $attributesByGroup = $this->attributesHelper->getAttributes(
            CookieGroup::ENTITY,
            $model->getDefaultAttributeSetId()
        );

        $groupSortOrder = 0;

        foreach ($attributesByGroup as $groupKey => $attributes) {
            $groupSortOrder += 10;
            $children = [];
            $attributeSortOrder = 0;
            /** @var Attribute $attribute */
            foreach ($attributes as $attribute) {
                $attributeSortOrder += 10;
                $children[$attribute->getAttributeCode()] = $this->uiComponentHelper->createField($attribute, $model, $attributeSortOrder);
            }

            if (count($children) <= 0) {
                continue;
            }

            $meta[$groupKey] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'fieldset',
                            'label' => __(ucfirst($groupKey)),
                            'sortOrder' => $groupSortOrder,
                            'collapsible' => true,
                            'opened' => true,
                        ],
                    ],
                ],
                'children' => $children,
            ];
        }

        return $meta;
    }

    public function getModel(): CookieGroup
    {
        /** @var CookieGroup $model */
        $model = $this->registry->registry('phpro_cookie_consent_cookie_group');

        return ($model instanceof CookieGroup) ? $model : $this->cookieGroupFactory->create();
    }
}
