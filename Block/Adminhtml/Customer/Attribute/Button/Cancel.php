<?php
/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mvn\Cam\Block\Adminhtml\Customer\Attribute\Button;

/**
 * Class Cancel
 */
class Cancel extends Generic
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/index')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
