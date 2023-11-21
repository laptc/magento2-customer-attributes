<?php

/**
 * Created on Fri Mar 10 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetNameAttributes implements ObserverInterface
{
    const ATTRIBUTES = ["prefix", "suffix", "middlename", "lastname", "firstname"];

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $attributes  = $observer->getEvent()->getAttributes();
        $nameAttributes = array_filter($attributes, function ($attribute) {
            return in_array($attribute->getAttributeCode(), static::ATTRIBUTES);
        });
        foreach ($nameAttributes as $attribute) {
            $block->setData($attribute->getAttributeCode(), $attribute);
        }
    }
}
