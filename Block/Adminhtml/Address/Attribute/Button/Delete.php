<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mvn\Cam\Block\Adminhtml\Address\Attribute\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if($this->getContext()->getRequestParam('attribute_id')){
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'id' => 'attribute-delete-button',
                'data_attribute'=> [
                    'url' => $this->getDeleteUrl(),
                ],
                'on_click' =>'deleteConfirm("'.__('Are you sure you want to delete this attribute? Only do this if you know what you are doing.').'","'.$this->getDeleteUrl().'")',
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
        return $this->getUrl('*/*/delete', ['attribute_id' => $this->getContext()->getRequestParam('attribute_id')]);
    }
}
