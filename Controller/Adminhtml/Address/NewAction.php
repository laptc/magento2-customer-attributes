<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mvn\Cam\Controller\Adminhtml\Address;

/**
 * Class NewAction
 * @package Mvn\Cam\Controller\Adminhtml\Address
 */
class NewAction extends \Mvn\Cam\Controller\Adminhtml\Address\Attribute
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->createForwardResult()->forward('edit');
    }
}
