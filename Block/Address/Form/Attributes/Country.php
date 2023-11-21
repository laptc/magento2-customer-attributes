<?php

/**
 * Created on Wed Jan 25 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Block\Address\Form\Attributes;

use Magento\Framework\View\Element\Template;
use Magento\Directory\Block\Data as DirectoryBlock;

class Country extends Template
{
    protected $_template = "Tangkoko_CustomerAttributesManagement::address/form/attributes/country.phtml";
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock("Magento\Directory\Block\Data")->setCountryId($this->getCountryId())->getCountryHtmlSelect();
    }
}
