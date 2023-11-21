<?php

/**
 * Created on Mon Nov 20 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Context;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;


class FrontendContext implements ContextInterface
{
    /**
     *
     * @var Session
     */
    private Session $session;

    /**
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Return customer
     *
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->session->getCustomer();
    }
}
