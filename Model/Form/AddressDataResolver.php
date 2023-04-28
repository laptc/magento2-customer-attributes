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

    private CustomerDataResolver $customerDataResolver;

    /**
     * Contructor
     *
     * @param LayoutInterface $layout
     * @param AddressFactory $addressFactory
     * @param CustomerDataResolver $customerDataResolver
     */
    public function __construct(
        LayoutInterface $layout,
        AddressFactory $addressFactory,
        CustomerDataResolver $customerDataResolver
    ) {
        $this->layout = $layout;
        $this->addressFactory = $addressFactory;
        $this->customerDataResolver = $customerDataResolver;
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
        $address = $this->addressFactory->create()->load($addressData->getId());
        $address->setData(array_merge($this->customerDataResolver->getFormData()->toArray(), $address->getData()));
        return $address;
    }
}
