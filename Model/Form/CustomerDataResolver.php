<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Form;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;

class CustomerDataResolver implements DataResolverInterface
{

    private Session $customerSession;

    private StoreManagerInterface $storeManager;

    /**
     *
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Session $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Return data to display in form
     *
     * @return Customer
     */
    public function getFormData(): Customer
    {
        if (!$this->customerSession->getCustomerId()) {
            $this->customerSession->getCustomer()->setWebsiteId($this->storeManager->getWebsite()->getId());
        }
        return $this->customerSession->getCustomer();
    }
}
