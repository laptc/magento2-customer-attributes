<?php

/**
 * Created on Sat Mar 04 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Attribute;


interface ProviderInterface
{
    /**
     * Return attributes
     *
     * @return \Magento\Eav\Model\Attribute[]
     */
    public function getAttributes(string $formCode): array;
}
