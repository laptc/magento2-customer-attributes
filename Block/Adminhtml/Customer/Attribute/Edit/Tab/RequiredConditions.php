<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Adminhtml\Customer\Attribute\Edit\Tab;

use Magento\Framework\Data\Form\Element\AbstractElement;

class RequiredConditions implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getRequiredConditions()) {
            return $element->getRule()->getRequiredConditions()->asHtmlRecursive();
        }
        return '';
    }
}
