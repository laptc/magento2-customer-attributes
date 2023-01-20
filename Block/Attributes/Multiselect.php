<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Attributes;

/**
 * Class Multiselect
 * @package Tangkoko\CustomerAttributesManagement\Block\Attributes
 */
class Multiselect extends AbstractElement
{

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Mvn_Cam::attributes/multiselect.phtml";

    /**
     * @return array
     */
    public function getAttributeValue()
    {
        return explode(",", parent::getAttributeValue());
    }
}
