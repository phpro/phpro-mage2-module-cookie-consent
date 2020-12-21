<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Controller\Adminhtml\CookieGroup;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Phpro\CookieConsent\Api\CookieGroupRepositoryInterface;
use Phpro\CookieConsent\Api\Data\CookieGroupInterface;
use Phpro\CookieConsent\Model\CookieGroup;
use Phpro\CookieConsent\Model\CookieGroupFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Phpro_CookieConsent::CookieGroup_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CookieGroupRepositoryInterface
     */
    private $cookieGroupRepository;

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CookieGroupInterface
     */
    private $defaultModel;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CookieGroupRepositoryInterface $cookieGroupRepository,
        CookieGroupFactory $cookieGroupFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $continueEditing = (bool) $this->getRequest()->getParam('back');
        $data = $this->getRequest()->getPostValue();
        $storeId = (isset($data['store_id'])) ? (int) $data['store_id'] : 0;

        $resultRedirect = $this->resultRedirectFactory->create();

        // Redirect to the index page if no data was posted:
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model = $this->findOrCreateModel($id, $storeId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage() . __('This Cookie Group no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            // Add the POST data to the model
            $model->setData($data);
            $model->setStoreId($storeId);
            // Check "Use Default Value" checkboxes values
            if (isset($data['use_default']) && !empty($data['use_default'])) {
                foreach ($data['use_default'] as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $model->setData($attributeCode, $this->getDefaultModel((int) $data['entity_id'])->getData($attributeCode));
                    }
                }
            }

            // Persist the data
            $this->cookieGroupRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the Cookie Group.'));
            $this->dataPersistor->clear('phpro_cookie_consent_cookie_group');

            if ($continueEditing) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), 'store' => $storeId]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Cookie Group.'));
        }

        $this->dataPersistor->set('phpro_cookie_consent_cookie_group', $data);

        return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
    }

    private function findOrCreateModel(int $id, $storeId): CookieGroupInterface
    {
        if (!$id) {
            /** @var CookieGroup $model */
            $model = $this->cookieGroupFactory->create();

            return $model;
        }

        return $this->cookieGroupRepository->getById($id, $storeId);
    }

    private function getDefaultModel(int $id): CookieGroupInterface
    {
        if ($this->defaultModel) {
            return $this->defaultModel;
        }

        $this->defaultModel = $this->cookieGroupRepository->getById($id, Store::DEFAULT_STORE_ID);

        return $this->defaultModel;
    }
}
