<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Form;

use Elasticsearch\Serializers\ArrayToJSONSerializer;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes\SpecialBlockProviderInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;
use Magento\Framework\View\Element\Template;

/**
 * Class Attributes
 * @package Tangkoko\CustomerAttributesManagement
 * @method \Tangkoko\CustomerAttributesManagement\ViewModel\Form\Attributes getViewModel()
 */
class Attributes extends \Magento\Framework\View\Element\Template
{


    protected AttributeFactory $attributeFactory;

    protected SerializerInterface $serializer;

    /**
     * Constructor
     *
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        SerializerInterface $serializer,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeFactory =  $attributeFactory;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        if ($this->getFormCode()) {

            foreach ($this->getViewModel()->getAttributes($this->getFormCode()) as $index => $attribute) {
                if ($attribute->getIsUserDefined()) {
                    $block = $this->attributeFactory->create($attribute, $this->getFormData());
                }
                if ($block) {
                    $this->setChild($this->getNameInLayout() . '.' .  $attribute->getAttributeCode(), $block);
                }
            }
        }

        return parent::_prepareLayout();
    }



    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData(): DataObject
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $data = $this->getViewModel()->getFormData();
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Return attribute configuration json encoded
     *
     * @return string
     */
    public function getJsonConfig()
    {

        return $this->serializer->serialize(
            ["conditions" => $this->getViewModel()->getRules($this->getFormCode()), "model" => $this->getViewModel()->getFormData()->toArray()]
        );
    }
}
