<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Block\Customer\Form\Register;

/**
 * Class Attributes
 * @package Mvn\Cam\Block\Customer\Form\Register
 */
class Attributes extends  \Mvn\Cam\Block\Customer\Form\Attributes
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
    protected $_template = "Mvn_Cam::customer/form/register/attributes.phtml";
}
