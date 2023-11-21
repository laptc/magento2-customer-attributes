<?php

/**
 * Created on Wed Jan 25 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Block\Address\Form\Attributes;

use Magento\Framework\View\Element\Template;

class Region extends Template
{
    protected $_template = "Tangkoko_CustomerAttributesManagement::address/form/attributes/region.phtml";

    /**
     * Get config value.
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
