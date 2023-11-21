<?php

/**
 * Created on Mon Nov 20 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model;

use Magento\Eav\Model\Attribute as Subject;


class Attribute
{


    public function afterGetIsRequired(Subject $subject, $result)
    {
        if ($subject->hasCamIsRequired()) {
            return (bool)$result && $subject->getCamIsRequired();
        }
        return $result;
    }
}
