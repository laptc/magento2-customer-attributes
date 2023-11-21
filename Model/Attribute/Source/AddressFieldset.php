<?php

/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Model\Attribute\Source;

class AddressFieldset implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var array
     */
    private $optionsArray;

    /**
     * AddressFieldset constructor.
     * @param array $optionsArray
     */
    public function __construct(array $optionsArray = [])
    {
        $this->optionsArray = $optionsArray;
    }

    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        //sort array elements using key value
        $options = [];
        foreach ($this->optionsArray as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
