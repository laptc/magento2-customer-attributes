<?php

/**
 * Created on Sat Jan 28 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\Entity\Attribute;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Tangkoko\CustomerAttributesManagement\Model\Context\ContextInterface;

class Backend
{

    private ContextInterface $context;

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * validate attribute value
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend $subject
     * @param callable $proceed
     * @param \Magento\Framework\DataObject $object
     * @return void
     */
    public function aroundValidate(\Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend $subject, callable $proceed, \Magento\Framework\DataObject $object)
    {
        /**
         * @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
         */
        $attribute = $subject->getAttribute();

        if (!in_array($subject->getAttribute()->getEntityType()->getEntityTypeCode(), [AddressMetadataInterface::ENTITY_TYPE_ADDRESS, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER]) || !$attribute->getExtensionAttributes() || !$attribute->getExtensionAttributes()->getCamAttribute()) {
            return $proceed($object);
        }
        $byPassRequired = false;
        if ($attribute->getIsRequired() && $attribute->getExtensionAttributes()->getCamAttribute()) {
            $byPassRequired = true;
            $isRequired = $attribute->getExtensionAttributes()->getCamAttribute()->isRequired($this->context->getCustomer());
            $attribute->setCamIsRequired($isRequired);
        }
        $byPassValidate = false;
        if ($attribute->getIsVisible() && !$attribute->getExtensionAttributes()->getCamAttribute()->validate($object)) {
            $attribute->setData("scope_is_visible", false);
            $byPassValidate = true;
        }
        $returnValue = $proceed($object);

        if ($byPassValidate) {
            $attribute->setData("scope_is_visible", true);
        }

        if ($byPassRequired) {
            $attribute->setIsRequired(true);
        }


        return $returnValue;
    }
}
