<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Attributes;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class AbstractElement
 * @package Tangkoko\CustomerAttributesManagement\Block\Attributes
 */
class AbstractElement extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonEncoder;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     */
    public function getAttribute()
    {
        return parent::getAttribute();
    }

    /**
     * @return string
     */
    public function getAttributeValue()
    {

        $value = "";
        $data = $this->getFormData();

        $attribute = $this->getAttribute();
        if ($attribute) {
            if ($data instanceof \Magento\Framework\DataObject && $data->hasData($attribute->getAttributeCode())) {
                $value = $data->getData($attribute->getAttributeCode());
            } else {
                $value = $this->getDefaultValue();
            }
        }
        return $value;
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return string
     */
    public function getValidationRules($attribute)
    {
        $rules = [];
        if ($attribute->isRequired()) {
            $rules["required"] = true;
        }
        foreach ($attribute->getValidationRules() as $rule) {
            $rules[$rule->getName()] = $rule->getValue();
        }
        return $this->jsonEncoder->serialize($rules);
    }
}
