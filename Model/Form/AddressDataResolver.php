<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Form;

use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Data\Address;
use Magento\Customer\Model\Address as AddressModel;
use Magento\Framework\View\LayoutInterface;

class AddressDataResolver implements DataResolverInterface
{

    private LayoutInterface $layout;

    private AddressFactory $addressFactory;

    public function __construct(
        LayoutInterface $layout,
        AddressFactory $addressFactory
    ) {
        $this->layout = $layout;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Return data to display in form
     *
     * @return Address
     */
    public function getFormData(): AddressModel
    {
        /**
         * @var Address $addressData
         */
        $addressData =  $this->layout->getBlock("customer_address_edit")->getAddress();

        return $this->addressFactory->create()->load($addressData->getId());
    }
}
