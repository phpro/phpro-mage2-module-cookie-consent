<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Controller\Adminhtml\CookieGroup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\Store;
use Phpro\CookieConsent\Api\CookieGroupRepositoryInterface;
use Phpro\CookieConsent\Model\CookieGroup;
use Phpro\CookieConsent\Model\CookieGroupFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Phpro_CookieConsent::CookieGroup_edit';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CookieGroupRepositoryInterface $cookieGroupRepository,
        CookieGroupFactory $cookieGroupFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $storeId = (int) $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Content'), __('Content'))
            ->addBreadcrumb(__('Cookie Consent'), __('Cookie Consent'))
            ->addBreadcrumb(__('Cookie Group'), __('Cookie Group'));

        // New action:
        if (!$id) {
            /** @var CookieGroup $model */
            $model = $this->cookieGroupFactory->create();
            $model->setStoreId($storeId);
            $this->registry->register('phpro_cookie_consent_cookie_group', $model);

            $pageTitle = __('New Cookie Group');
            $resultPage->addBreadcrumb($pageTitle, $pageTitle);
            $resultPage->getConfig()->getTitle()->prepend($pageTitle);

            return $resultPage;
        }

        // Edit action:
        try {
            $model = $this->cookieGroupRepository->getById($id, $storeId);
            $model->setStoreId($storeId);
            $this->registry->register('phpro_cookie_consent_cookie_group', $model);

            $pageTitle = __('Edit Cookie Group');
            $resultPage->addBreadcrumb($pageTitle, $pageTitle);
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Cookie Group: %1', $model->getName()));

            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('This Cookie Group no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }
    }
}
