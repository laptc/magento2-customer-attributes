<?php

/**
 * Copyright © 2019 MVN. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Tangkoko\CustomerAttributesManagement\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tangkoko\CustomerAttributesManagement\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tangkoko\CustomerAttributesManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createJsonResult($data)
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createPageResult()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        return $resultPage;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createRedirectResult()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createForwardResult()
    {
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $resultForward;
    }
}
