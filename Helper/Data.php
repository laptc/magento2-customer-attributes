<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Helper;

/**
 * Class Data
 * @package Tangkoko\CustomerAttributesManagement\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_STREET_PLACEHOLDER_CONFIG_PATH = "cam/address/street_placeholders";

    /**
     * Return steet place holders
     *
     * @return string[]
     */
    public function getStreetPlaceholders(): array
    {
        return explode("\n", $this->scopeConfig->getValue(static::XML_STREET_PLACEHOLDER_CONFIG_PATH) ?? "");
    }
}
