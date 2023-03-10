<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Form;

use Magento\Framework\Model\AbstractModel;

interface DataResolverInterface
{
    /**
     * Return data to display in form
     *
     * @return AbstractModel
     */
    public function getFormData(): AbstractModel;
}
