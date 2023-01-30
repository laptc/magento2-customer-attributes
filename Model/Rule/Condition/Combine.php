<?php

/**
 * Created on Sun Jan 29 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Rule\Condition;

use Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface;

/**
 * @api
 * @since 100.0.2
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Customer
     */
    protected $_conditionCustomer;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Customer $conditionCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Customer $conditionCustomer,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionCustomer = $conditionCustomer;
        parent::__construct($context, $data);
        $this->setType(\Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {

        $customerAttributes = $this->_conditionCustomer->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($customerAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Customer|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine::class,
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Customer Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('cam_customer_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
