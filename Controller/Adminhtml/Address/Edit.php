<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address;

/**
 * Class Edit
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address
 */
class Edit extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address\Attribute
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('attribute_code');

        $model = $this->attributeFactory->createAttribute(\Magento\Customer\Model\Attribute::class);
        if ($code) {
            $model =  $this->attributeRepository->get($this->entityTypeId, $code);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                $resultRedirect = $this->createRedirectResult();
                return $resultRedirect->setPath('cam/*/');
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addErrorMessage(__('This attribute cannot be edited.'));
                $resultRedirect = $this->createRedirectResult();
                return $resultRedirect->setPath('cam/*/');
            }
        }

        $data = $this->_session->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $code === null) {
            $model->addData($attributeData);
        }
        $this->coreRegistry->register('entity_attribute', $model);

        $title = $code ? __('Edit Customer Address Attribute "%1', $model->getAttributeCode()) : __('New Customer Address Attribute');
        $resultPage = $this->createPageResult();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
