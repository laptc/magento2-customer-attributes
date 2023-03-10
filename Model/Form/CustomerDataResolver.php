<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Form;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;

class CustomerDataResolver implements DataResolverInterface
{

    private Session $customerSession;

    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Return data to display in form
     *
     * @return Customer
     */
    public function getFormData(): Customer
    {
        return $this->customerSession->getCustomer();
    }
}
