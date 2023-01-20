<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer;

/**
 * Class Index
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer
 */
class Index extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->createPageResult();
        $resultPage->setActiveMenu('Tangkoko_CustomerAttributesManagement::customer_attributes');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Attributes'));
        $resultPage->addBreadcrumb(__('Customer Attributes'), __('Customer Attributes'));
        return $resultPage;
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Tangkoko_CustomerAttributesManagement::customer_attributes');
    }
}
