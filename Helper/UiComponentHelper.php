<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Helper;

use Magento\Backend\Model\UrlInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\Entity\AttributeFactory as EavAttributeFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\Store;
use Phpro\CookieConsent\Api\Data\EavModelInterface;
use Phpro\CookieConsent\Model\Eav\ScopeOverriddenValue;

class UiComponentHelper
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var EavAttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Filesystem
     */
    private $filesystem;


    public function __construct(
        ArrayManager $arrayManager,
        EavAttributeFactory $eavAttributeFactory,
        ScopeOverriddenValue $scopeOverriddenValue,
        UrlInterface $backendUrl,
        Repository $assetRepo,
        Filesystem $filesystem
    ) {
        $this->arrayManager = $arrayManager;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->backendUrl = $backendUrl;
        $this->assetRepo = $assetRepo;
        $this->filesystem = $filesystem;
    }

    public function createField(AttributeInterface $attribute, EavModelInterface $model, int $sortOrder, array $disabledFields = [])
    {
        $configPath = 'arguments/data/config';

        $isDefaultStore = ((int) $model->getStoreId() === Store::DEFAULT_STORE_ID);
        $attributeData = $this->arrayManager->set($configPath, [], [
            'disabled' => !$isDefaultStore && ((bool) $attribute->getIsGlobal()) || in_array($attribute->getAttributeCode(), $disabledFields, true),
            'componentType' => 'field',
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->mapElementType($attribute),
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote(),
            'default' => $attribute->getDefaultValue(),
            'label' => ($attribute->getDefaultFrontendLabel()) ?? $attribute->getAttributeCode(),
            'sortOrder' => $sortOrder,
        ]);

        $attributeModel = $this->getAttributeModel($attribute);
        if ($attributeModel->usesSource()) {
            $attributeData = $this->arrayManager->merge($configPath, $attributeData, [
                'options' => $attributeModel->getSource()->getAllOptions(),
            ]);
        }

        if ($attribute->getIsRequired()) {
            $attributeData = $this->arrayManager->merge($configPath, $attributeData, [
                'validation' => ['required-entry' => true],
            ]);
        }

        if ($attribute->getAttributeCode() === 'url_key') {
            $attributeData = $this->arrayManager->merge($configPath, $attributeData, [
                'validation' => ['no-whitespace' => true],
            ]);
        }

        if ($attribute->getFrontendInput() === 'boolean') {
            $attributeData = $this->arrayManager->merge($configPath, $attributeData, [
                'prefer' => 'toggle',
                'checked' => ($attribute->getDefaultValue() === 1) ? true : false,
                'valueMap' => ['true' => '1', 'false' => '0'],
            ]);
        }

        $attributeData = $this->addUseDefaultValueCheckbox($attribute, $model, $attributeData);

        return $attributeData;
    }

    private function getAttributeModel(AttributeInterface $attribute)
    {
        return $this->eavAttributeFactory->create()->load($attribute->getAttributeId());
    }

    private function addUseDefaultValueCheckbox(AttributeInterface $attribute, EavModelInterface $model, array $meta)
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute, $model);
        if ($canDisplayService) {
            $meta['arguments']['data']['config']['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            $meta['arguments']['data']['config']['disabled'] = !$this->scopeOverriddenValue->containsValue(
                $model->getEntityType(),
                $model,
                $attribute->getAttributeCode(),
                $model->getStoreId()
            );
        }

        return $meta;
    }

    private function canDisplayUseDefault(AttributeInterface $attribute, EavModelInterface $model)
    {
        return
            (!(bool) $attribute->getIsGlobal())
            && $model
            && $model->getId()
            && $model->getStoreId()
        ;
    }

    private function mapElementType(AttributeInterface $attribute): string
    {
        $elementType = $attribute->getFrontendInput();

        if ($elementType === 'text') {
            return 'input';
        }

        if ($elementType === 'boolean') {
            return 'checkbox';
        }

        if (strpos($elementType, 'wysiwyg') !== false) {
            return 'wysiwyg';
        }

        return $elementType;
    }
}
