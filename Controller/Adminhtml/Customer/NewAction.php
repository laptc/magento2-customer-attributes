<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer;

/**
 * Class NewAction
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer
 */
class NewAction extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer\Attribute
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->createForwardResult()->forward('edit');
    }
}
