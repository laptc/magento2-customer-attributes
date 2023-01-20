<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address;

/**
 * Class Index
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address
 */
class Index extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->createPageResult();
        $resultPage->setActiveMenu('Mvn_Cam::address_attributes');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Address Attributes'));
        $resultPage->addBreadcrumb(__('Customer Address Attributes'), __('Customer Address Attributes'));
        return $resultPage;
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mvn_Cam::address_attributes');
    }
}
