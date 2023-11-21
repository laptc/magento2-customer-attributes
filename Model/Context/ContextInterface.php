<?php

/**
 * Created on Mon Nov 20 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Context;

use Magento\Customer\Model\Customer;

interface ContextInterface
{
    public function getCustomer(): Customer;
}
