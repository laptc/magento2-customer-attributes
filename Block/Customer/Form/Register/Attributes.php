<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Register;

/**
 * Class Attributes
 * @package Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Register
 */
class Attributes extends  \Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Attributes
{

    /**
     * @var string
     */
    protected $code = "customer_account_create";

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Tangkoko_CustomerAttributesManagement::customer/form/register/attributes.phtml";
}
