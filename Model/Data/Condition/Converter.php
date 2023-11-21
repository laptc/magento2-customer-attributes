<?php

/**
 * Created on Fri Jan 27 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Data\Condition;

class Converter
{
    /**
     * @var \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterfaceFactory
     */
    protected $conditionFactory;

    /**
     * @param \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterfaceFactory $conditionFactory
     */
    public function __construct(\Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterfaceFactory $conditionFactory)
    {
        $this->conditionFactory = $conditionFactory;
    }

    /**
     * @param \Magento\Rule\Model\Condition\ConditionInterface $dataModel
     * @return array
     */
    public function dataModelToArray(\Magento\Rule\Model\Condition\ConditionInterface $dataModel)
    {
        $conditionArray = [
            'type' => $dataModel->getType(),
            'attribute' => $dataModel->getAttribute(),
            'operator' => $dataModel->getOperator(),
            'value' => $dataModel->getValue(),
            'is_value_processed' => $dataModel->getIsValueParsed(),
            'aggregator' => $dataModel->getAggregator()
        ];

        foreach ((array)$dataModel->getConditions() as $condition) {
            $conditionArray['conditions'][] = $this->dataModelToArray($condition);
        }
        return $conditionArray;
    }

    /**
     * @param array $conditionArray
     * @return \Magento\Rule\Model\Condition\ConditionInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function arrayToDataModel(array $conditionArray)
    {
        /** @var \Magento\Rule\Model\Condition\ConditionInterface $condition */
        $condition = $this->conditionFactory->create();

        $condition->setType($conditionArray['type']);
        $condition->setAggregator(isset($conditionArray['aggregator']) ? $conditionArray['aggregator'] : false);
        $condition->setAttribute(isset($conditionArray['attribute']) ? $conditionArray['attribute'] : false);
        $condition->setOperator(isset($conditionArray['operator']) ? $conditionArray['operator'] : false);
        $condition->setValue(isset($conditionArray['value']) ? $conditionArray['value'] : false);
        $condition->setIsValueParsed(
            isset($conditionArray['is_value_parsed']) ? $conditionArray['is_value_parsed'] : false
        );

        if (isset($conditionArray['conditions']) && is_array($conditionArray['conditions'])) {
            $conditions = [];
            foreach ($conditionArray['conditions'] as $condition) {
                $conditions[] = $this->arrayToDataModel($condition);
            }
            $condition->setConditions($conditions);
        }
        return $condition;
    }
}
