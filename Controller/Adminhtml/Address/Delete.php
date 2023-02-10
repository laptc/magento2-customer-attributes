<?php

/**
 *
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer;

class Delete extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer\Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('attribute_code');
        $resultRedirect = $this->createRedirectResult();
        if ($code) {
            $model =  $this->attributeRepository->get($this->entityTypeId, $code);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                $resultRedirect = $this->createRedirectResult();
                return $resultRedirect->setPath('cam/*/');
            }

            if ($model->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the attribute.'));
                return $resultRedirect->setPath('cam/*/');
            }

            try {
                $attributeCode = $model->getAttributeCode();
                $this->attributeRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the customer address attribute: "%1".', $attributeCode));
                return $resultRedirect->setPath('cam/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    'cam/*/edit',
                    ['attribute_code' => $this->getRequest()->getParam('attribute_code')]
                );
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an attribute to delete.'));
        return $resultRedirect->setPath('cam/*/');
    }
}
