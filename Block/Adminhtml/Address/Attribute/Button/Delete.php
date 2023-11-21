<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Adminhtml\Address\Attribute\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{


    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getContext()->getRequestParam('attribute_code')) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'id' => 'attribute-delete-button',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this attribute ?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
            return $data;
        }
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['attribute_code' => $this->getContext()->getRequestParam('attribute_code')]);
    }
}
