<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup\CollectionFactory;

class Overview extends Template implements BlockInterface
{
    protected $_template = "Phpro_CookieConsent::widget/overview.phtml";

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getCookieGroups(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($this->storeManager->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', 1);

        return $collection->getItems();
    }
}
