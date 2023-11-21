<?php

/**
 * Created on Tue Nov 21 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Observer\Admin\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Tangkoko\CustomerAttributesManagement\Model\Context\AdminContext;
use Magento\Customer\Model\CustomerFactory;

class PrepareSave implements ObserverInterface
{
    private AdminContext $context;

    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;

    public function __construct(AdminContext $context, CustomerFactory $customerFactory)
    {
        $this->context = $context;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = clone $observer->getEvent()->getCustomer();
        $customerModel = $this->customerFactory->create()->updateData(
            $customer->setAddresses([])
        );
        $this->context->setCustomer($customerModel);
    }
}
