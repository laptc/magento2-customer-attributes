<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Model\CamAttribute\Converter;

use Tangkoko\CustomerAttributesManagement\Model\Data\Condition;
use Tangkoko\CustomerAttributesManagement\Api\Data\CamAttributeInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;
use Magento\Framework\Serialize\Serializer\Json;

class ToDataModel
{
    /**
     * @var CamAttributeFactory
     */
    protected $camAttributeFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var Json $serializer
     */
    private $serializer;


    /**
     *
     * @var \Tangkoko\CustomerAttributesManagement\Model\Data\ConditionFactory
     */
    private $conditionDataFactory;


    /**
     *
     * @param \Magento\SalesRule\Model\RuleFactory $CamAttributeFactory
     * @param \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterfaceFactory $conditionDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $camAttributeFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Tangkoko\CustomerAttributesManagement\Model\Data\ConditionFactory $conditionDataFactory,
        Json $serializer
    ) {
        $this->camAttributeFactory = $camAttributeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->conditionDataFactory = $conditionDataFactory;
    }


    /**
     * @param CamAttributeInterface $dataModel
     * @return $this
     */
    public function ConvertConditions(CamAttributeInterface $dataModel)
    {
        $conditionSerialized = $dataModel->getVisibilityConditionsSerialized();
        if ($conditionSerialized) {
            $conditionArray = $this->serializer->unserialize($conditionSerialized);
            $conditionDataModel = $this->arrayToConditionDataModel($conditionArray);
            $dataModel->setVisibilityConditions($conditionDataModel);
        } else {
            $dataModel->setVisibilityConditions($this->conditionDataFactory->create());
        }
        return $this;
    }


    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return Condition
     */
    protected function arrayToConditionDataModel(array $input)
    {
        /** @var \Tangkoko\CustomerAttributesManagement\Model\Data\Condition $conditionDataModel */
        $conditionDataModel = $this->conditionDataFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $conditionDataModel->setConditionType($value);
                    break;
                case 'attribute':
                    $conditionDataModel->setAttributeName($value);
                    break;
                case 'operator':
                    $conditionDataModel->setOperator($value);
                    break;
                case 'value':
                    $conditionDataModel->setValue($value);
                    break;
                case 'aggregator':
                    $conditionDataModel->setAggregatorType($value);
                    break;
                case 'conditions':
                    $conditions = [];
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToConditionDataModel($condition);
                    }
                    $conditionDataModel->setConditions($conditions);
                    break;
                default:
            }
        }
        return $conditionDataModel;
    }
}
