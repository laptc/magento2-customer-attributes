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

    const BASE_ADDRESS_ATTRIBUTES = [
        "postcode",
        "street",
        "lastname",
        "region_id",
        "city",
        "country_id"
    ];

    /**
     *
     * @var string[]
     */
    protected array $fields = [];


    protected $attributes = [];

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
            $this->prepareFieldSetsField();
            $this->createChildBlocks();
        }

        return parent::_prepareLayout();
    }

    protected function createChildBlocks()
    {
        foreach ($this->getViewModel()->getAttributes($this->getFormCode()) as $attributeCode => $attribute) {
            $block = null;
            if ($attribute->getIsUserDefined() || in_array($attribute->getAttributeCode(), self::BASE_ADDRESS_ATTRIBUTES)) {
                $block = $this->attributeFactory->create($attribute, $this->getFormData());
                if ($attributeViewModel = $this->getViewModel()->getAttributeViewModel()) {
                    $block->setViewModel($attributeViewModel);
                }
            }
            if ($block) {
                $this->setChild($this->getNameInLayout() . '.' .  $attribute->getAttributeCode(), $block);
            }
        }
    }

    /**
     * Prepare system fields to display
     *
     * @return void
     */
    protected function prepareFieldSetsField(): void
    {
        if ($this->getFieldsets() && is_array($this->getFieldsets())) {
            foreach ($this->getFieldsets() as $fieldsetName => $fieldset) {
                if (isset($fieldset["fields"]) && is_array($fieldset["fields"])) {
                    $this->fields += $fieldset["fields"];
                }
            }
        }
    }

    public function getAttributes(string $fieldsetName = null)
    {
        $attributes = $this->getViewModel()->getAttributes($this->getFormCode());
        if (is_null($fieldsetName)) {
            return $attributes;
        }


        $fieldsets = $this->getFieldsets();
        $fieldsetAttributes = [];
        if (isset($fieldsets[$fieldsetName])) {
            if (isset($fieldsets[$fieldsetName]["fields"]) && is_array($fieldsets[$fieldsetName]["fields"])) {
                $fields = $fieldsets[$fieldsetName]["fields"];
                $fieldsetAttributes = array_filter(
                    $attributes,
                    function ($attribute) use ($fields) {
                        if (array_key_exists($attribute->getAttributeCode(), $fields)) {
                            return true;
                        }
                        return false;
                    }
                );
            } else {
                $fieldsetAttributes = array_filter(
                    $attributes,
                    function ($attribute) use ($fieldsets) {
                        foreach ($fieldsets as $fieldset) {
                            if (isset($fieldset["fields"])) {
                                if (array_key_exists($attribute->getAttributeCode(), $fieldset["fields"])) {
                                    return false;
                                }
                            }
                        }
                        return true;
                    }
                );
            }
        }

        return $fieldsetAttributes;
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
