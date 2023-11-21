<?php

/**
 * Created on Mon Nov 20 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Context;

use Magento\Customer\Model\Customer;
use Magento\Framework\Session\SessionManagerInterface;

class AdminContext implements ContextInterface
{
    private Customer $customer;

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Return customer
     *
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
