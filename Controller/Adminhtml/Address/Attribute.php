<?php

/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address;

use Magento\Eav\Api\AttributeRepositoryInterface;
use \Magento\Eav\Model\EntityFactory;

/**
 * Class Attribute
 * @package Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer
 */
abstract class Attribute extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Tangkoko_CustomerAttributesManagement::address_attributes';

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $attributeLabelCache;

    /**
     * @var string
     */
    protected $entityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     *
     * @var \Magento\Eav\Model\AttributeFactory
     */
    protected  $attributeFactory;

    /**
     *
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     *
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * Attribute constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tangkoko\CustomerAttributesManagement\Helper\Data $helper
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tangkoko\CustomerAttributesManagement\Helper\Data $helper,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\AttributeFactory $attributeFactory,
        AttributeRepositoryInterface $attributeRepository,
        EntityFactory $entityFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->attributeLabelCache = $attributeLabelCache;
        parent::__construct($context, $helper);
        $this->attributeFactory = $attributeFactory;
        $this->attributeRepository = $attributeRepository;
        $this->entityFactory = $entityFactory;
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->entityTypeId = $this->_objectManager->create(
            \Magento\Eav\Model\Entity::class
        )->setType(
            \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS
        )->getTypeId();
        return parent::dispatch($request);
    }
}
