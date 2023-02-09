<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Address\Form\Edit;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes\SpecialBlockProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;

/**
 * Class Attributes
 * @package Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Edit
 */
class Attributes extends  \Tangkoko\CustomerAttributesManagement\Block\Customer\Form\Attributes
{

    /**
     * @var string
     */
    protected $code = "customer_address_edit";

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Tangkoko_CustomerAttributesManagement::address/form/edit/attributes.phtml";

    /**
     * @var \Magento\Customer\Api\Data\AddressInterface|null
     */
    protected $_address = null;


    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressDataFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\MetadataInterface $metadata,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        SpecialBlockProviderInterface $specialBlockProvider,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Converter $converter,
        Json $serializer,
        array $data = []

    ) {
        parent::__construct($context, $customerSession, $metadata, $attributeFactory, $specialBlockProvider, $attributeRepository, $searchCriteriaBuilderFactory, $converter, $serializer, $data);
        $this->customerSession = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->addressDataFactory = $addressDataFactory;
    }


    protected function _prepareLayout()
    {
        $this->initAddressObject();
        parent::_prepareLayout();
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        $formData =  $this->_address->__toArray();
        if (isset($formData["custom_attributes"])) {
            foreach ($formData["custom_attributes"] as $customAttribute) {
                $formData[$customAttribute["attribute_code"]] = $customAttribute["value"];
            }
            $formData["region"] = $this->_address->getRegion() ? $this->_address->getRegion()->getRegion() : null;
        }
        $data = new \Magento\Framework\DataObject();
        $data->addData($formData);
        return  $data;
    }

    private function initAddressObject()
    {
        // Init address object
        if ($addressId = $this->getRequest()->getParam('id')) {
            try {
                $this->_address = $this->addressRepository->getById($addressId);
                if ($this->_address->getCustomerId() != $this->customerSession->getCustomerId()) {
                    $this->_address = null;
                }
            } catch (NoSuchEntityException $e) {
                $this->_address = null;
            }
        }

        if ($this->_address === null || !$this->_address->getId()) {
            $this->_address = $this->addressDataFactory->create();
            $customer = $this->customerSession->getCustomer();
            $this->_address->setPrefix($customer->getPrefix());
            $this->_address->setFirstname($customer->getFirstname());
            $this->_address->setMiddlename($customer->getMiddlename());
            $this->_address->setLastname($customer->getLastname());
            $this->_address->setSuffix($customer->getSuffix());
        }
    }

    /**
     * Return true if attribute is in fieldset
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return boolean
     */
    public function isInFielsdset($attribute)
    {
        $addressFieldset = $this->getAddressFieldset();

        if ($this->getFieldset() == "address") {
            return  isset($addressFieldset[$attribute->getAttributeCode()]) && $addressFieldset[$attribute->getAttributeCode()] === true;
        }
        return !isset($addressFieldset[$attribute->getAttributeCode()]) || $addressFieldset[$attribute->getAttributeCode()] === false;
    }
}
