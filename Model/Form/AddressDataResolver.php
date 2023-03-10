<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Form;

use Magento\Customer\Model\Data\Address;
use Magento\Framework\View\LayoutInterface;

class AddressDataResolver implements DataResolverInterface
{

    private LayoutInterface $layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Return data to display in form
     *
     * @return Address
     */
    public function getFormData(): Address
    {
        return $this->layout->getBlock("customer_address_edit")->getAddress();
    }
}
