<?php

/**
 * Created on Fri Feb 17 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource model initialization
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init(
            \Tangkoko\CustomerAttributesManagement\Model\Data\CamAttribute::class,
            \Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute::class
        );
    }
}
