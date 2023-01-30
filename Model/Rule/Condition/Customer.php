<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Model\Rule\Condition;

use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Address rule condition data model.
 */
class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     *
     * @var \Magento\Eav\Api\Data\AttributeInterface[]
     */
    protected $attributes;

    /**
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributeModels = $this->attributeRepository->getList(\Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $this->searchCriteriaBuilderFactory->create()->create());
        $this->attributes = [];
        $options = [];
        foreach ($attributeModels->getItems() as $attributeModel) {
            if (!in_array($attributeModel->getFrontendInput(), ["select", "multiselect"])) {
                continue;
            }

            $this->attributes[$attributeModel->getAttributeCode()] = $attributeModel;
            $options[$attributeModel->getAttributeCode()] = $attributeModel->getDefaultFrontendLabel();
        }

        asort($options);
        $this->setAttributeOption($options);
        return $this;
    }

    /**
     * Get attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->attributes[$this->getAttribute()]->getFrontendInput() == 'select') {
            return 'multiselect';
        };
        return $this->attributes[$this->getAttribute()]->getFrontendInput();
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->attributes[$this->getAttribute()]->getFrontendInput() == 'select') {
            return 'multiselect';
        };
        return $this->attributes[$this->getAttribute()]->getFrontendInput();
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $options = [];
            foreach ($this->attributes[$this->getAttribute()]->getOptions() as $option) {
                $options[] = ["value" => $option->getValue(), "label" =>  $option->getLabel()];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && !$address->hasPaymentMethod()) {
            $address->setPaymentMethod($model->getQuote()->getPayment()->getMethod());
        }

        return parent::validate($address);
    }
}
