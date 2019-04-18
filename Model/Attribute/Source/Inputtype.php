<?php
/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Model\Attribute\Source;

/**
 * Class Inputtype
 * @package Mvn\Cam\Model\Attribute\Source
 */
class Inputtype extends \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $optionsArray
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $coreRegistry,
        array $optionsArray = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($optionsArray);
    }

    /**
     * Get product input types as option array
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function toOptionArray()
    {
        $inputTypes = [
        ];

        $response = new \Magento\Framework\DataObject();
        $response->setTypes([]);
        $this->_eventManager->dispatch('adminhtml_customer_attribute_types', ['response' => $response]);
        $_hiddenFields = [];
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }

        if ($this->_coreRegistry->registry('attribute_type_hidden_fields') === null) {
            $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);
        }

        $normalTypes = parent::toOptionArray();
        $ignoreTypes = ['texteditor'];

        foreach ($normalTypes as $index => $type) {
            if (isset($type['value']) && in_array($type['value'], $ignoreTypes)) {
                unset($normalTypes[$index]);
            }
        }
        return array_merge($normalTypes, $inputTypes);
    }
}
