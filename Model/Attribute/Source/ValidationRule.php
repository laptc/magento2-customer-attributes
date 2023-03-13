<?php

/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Model\Attribute\Source;

/**
 * Class Options
 */
class ValidationRule implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $optionsArray;

    /**
     * CustomerFieldset constructor.
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
        ksort($this->optionsArray);
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
