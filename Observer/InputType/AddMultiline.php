<?php

/**
 * Created on Fri Mar 24 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Observer\InputType;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddMultiline implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $types = $observer->getEvent()->getResponse()->getTypes();
        $types["800"] = ["value" => "multiline", "label" => __("Text Multiline")];
        $observer->getEvent()->getResponse()->setTypes($types);
    }
}
