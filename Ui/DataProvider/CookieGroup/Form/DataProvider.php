<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Ui\DataProvider\CookieGroup\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Phpro\CookieConsent\Api\CookieGroupRepositoryInterface;
use Phpro\CookieConsent\Api\Data\CookieGroupInterface;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $modifiersPool,
        RequestInterface $request,
        CookieGroupRepositoryInterface $cookieGroupRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->modifiersPool = $modifiersPool;
        $this->request = $request;
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $storeId = $this->request->getParam('store');
        $entityId = $this->request->getParam('entity_id');

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        if ($storeId && $storeId !== 0) {
            $defaultModel = $this->cookieGroupRepository->getById((int) $entityId, (int) $storeId);
            $currentModel = $this->cookieGroupRepository->getById((int) $entityId, Store::DEFAULT_STORE_ID);
            $meta['general']['children']['system_name']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['is_essential']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['is_active']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['name']['arguments']['data']['config']['service']['template'] = 'ui/form/element/helper/service';
            $meta['general']['children']['name']['arguments']['data']['config']['disabled'] = $this->isAttributeDisabled('name', $defaultModel, $currentModel);
            $meta['general']['children']['description']['arguments']['data']['config']['service']['template'] = 'ui/form/element/helper/service';
            $meta['general']['children']['description']['arguments']['data']['config']['disabled'] = $this->isAttributeDisabled('description', $defaultModel, $currentModel);
        }

        return $meta;
    }

    private function isAttributeDisabled(string $attributeCode, CookieGroupInterface $default, CookieGroupInterface $current): bool
    {
        return $current->getData($attributeCode) === $default->getData($attributeCode);
    }
}
