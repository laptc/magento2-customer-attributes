<?php

/**
 * Created on Wed Jan 25 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Block\Address\Form\Attributes;

use Magento\Framework\View\Element\Template;

class Street extends Template
{
    protected $_template = "Tangkoko_CustomerAttributesManagement::address/form/attributes/street.phtml";

    /**
     * Return the specified numbered street line.
     *
     * @param int $lineNumber
     * @return string
     */
    public function getStreetLine($lineNumber)
    {
        $street = $this->getFormData()->getStreet();
        return isset($street[$lineNumber - 1]) ? $street[$lineNumber - 1] : '';
    }
}
