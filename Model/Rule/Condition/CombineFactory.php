<?php

namespace Tangkoko\CustomerAttributesManagement\Model\Rule\Condition;

/**
 * Factory class for @see \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine
 */
class CombineFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceNames = [];

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceNames = [])
    {
        $this->_objectManager = $objectManager;
        $this->_instanceNames = $instanceNames;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine
     */
    public function create($code, array $data = [])
    {
        return $this->_objectManager->create($this->_instanceNames[$code], $data);
    }
}
