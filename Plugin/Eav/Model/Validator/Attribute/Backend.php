<?php

/**
 * Created on Tue Nov 21 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\Validator\Attribute;

use Magento\Eav\Model\Validator\Attribute\Backend as Subject;
use Magento\Customer\Model\Customer;
use Tangkoko\CustomerAttributesManagement\Model\Context\AdminContext;

class Backend
{
    private AdminContext $context;

    public function __construct(AdminContext $context)
    {

        $this->context = $context;
    }

    public function beforeIsValid(Subject $subject, Customer $customer)
    {

        $this->context->setCustomer($customer);
        return [$customer];
    }
}
