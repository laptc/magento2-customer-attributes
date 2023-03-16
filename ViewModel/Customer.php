<?php

/**
 * Created on Mon Mar 13 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tangkoko\CustomerAttributesManagement\Helper\Data;

class Customer implements ArgumentInterface
{

    private Data $configHelper;


    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonEncoder;


    /**
     * Constructor
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonEncoder
     * @param Data $configHelper
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        Data $configHelper
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->configHelper = $configHelper;
    }


    /**
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return string
     */
    public function getValidationRules($attribute)
    {
        $rules = [];
        if ($attribute->getIsRequired()) {
            $rules["required"] = true;
        }
        return $this->jsonEncoder->serialize(array_merge($attribute->getValidationRules(), $rules));
    }
}
