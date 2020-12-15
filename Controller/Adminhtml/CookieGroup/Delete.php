<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Controller\Adminhtml\CookieGroup;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use PHPro\CookieConsent\Api\CookieGroupRepositoryInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'PHPro_CookieConsent::CookieGroup_delete';

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;

    public function __construct(
        Context $context,
        CookieGroupRepositoryInterface $cookieGroupRepository
    ) {
        parent::__construct($context);
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = (int) $this->getRequest()->getParam('entity_id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a Cookie Group to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->cookieGroupRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('You deleted the Cookie Group.'));

            return $resultRedirect->setPath('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/', ['entity_id' => $id]);
        }
    }
}
