<?php

/**
 * Created on Sun Jan 29 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Rule\Condition;

use Magento\Rule\Model\Condition\ConditionInterface as ConditionConditionInterface;

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
     *
     * @var ConditionInterface[][]
     */
    protected $conditions = [];

    /**
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param array $conditions
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $conditions = [],
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->conditions = $conditions;
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
        $groups = [
            [
                'value' => \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine::class,
                'label' => __('Conditions combination')
            ]
        ];
        foreach ($this->conditions as $code => $conditionArr) {
            $attributes = [];
            $options = $conditionArr["value"]->loadAttributeOptions()->getAttributeOption();
            foreach ($options as $code => $label) {
                $attributes[] = [
                    'value' => get_class($conditionArr["value"]) . '|' . $code,
                    'label' => $label,
                ];
            }
            $groups[] = ['label' => $conditionArr["label"], 'value' => $attributes];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            $groups
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('cam_customer_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * Is entity valid
     *
     * @param int|\Magento\Framework\Model\AbstractModel $entity
     * @return bool
     */
    protected function _isVisible($entity)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            if ($entity instanceof \Magento\Framework\Model\AbstractModel) {
                $validated = $cond->isVisible($entity);
            } else {
                $validated = $cond->validateByEntityId($entity);
            }
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }

    /**
     * Validate
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function isVisible(\Magento\Framework\Model\AbstractModel $model)
    {
        return $this->_isVisible($model);
    }
}
