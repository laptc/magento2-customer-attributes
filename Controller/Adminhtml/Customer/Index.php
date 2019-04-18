<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mvn\Cam\Controller\Adminhtml\Customer;

/**
 * Class Index
 * @package Mvn\Cam\Controller\Adminhtml\Customer
 */
class Index extends \Mvn\Cam\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->createPageResult();
        $resultPage->setActiveMenu('Mvn_Cam::customer_attributes');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Attributes'));
        $resultPage->addBreadcrumb(__('Customer Attributes'), __('Customer Attributes'));
        return $resultPage;
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mvn_Cam::customer_attributes');
    }
}