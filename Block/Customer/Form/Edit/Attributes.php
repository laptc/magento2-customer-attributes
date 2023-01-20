<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Edit;

/**
 * Class Attributes
 * @package Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Edit
 */
class Attributes extends  \Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Attributes
{

    /**
     * @var string
     */
    protected $code = "customer_account_edit";

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Mvn_Cam::customer/form/edit/attributes.phtml";
}
