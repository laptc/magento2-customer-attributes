<?php

/**
 * Created on Sat Jan 28 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\Entity\Attribute;

class Backend
{

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

        $attribute = $subject->getAttribute();
        if (!$attribute->getExtensionAttributes() || !$attribute->getExtensionAttributes()->getCamAttribute()) {
            return $proceed($object);
        }
        $byPassValidate = false;
        if ($attribute->getIsVisible() && !$attribute->getExtensionAttributes()->getCamAttribute()->isVisible($object)) {
            $attribute->setIsVisible(false);
            $byPassValidate = true;
        }
        $returnValue = $proceed($object);
        if ($byPassValidate) {
            $attribute->setIsVisible(true);
        }
        return $returnValue;
    }
}
