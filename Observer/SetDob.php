<?php

/**
 * Created on Fri Mar 10 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetDob implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        $block = $observer->getEvent()->getBlock();
        $block->setDate($object->getDob());
    }
}
