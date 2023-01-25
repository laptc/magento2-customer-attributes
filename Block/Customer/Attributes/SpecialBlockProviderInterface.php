<?php

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes;

use Magento\Framework\View\Element\AbstractBlock;

interface SpecialBlockProviderInterface
{
    /**
     * Return Block
     *
     * @param DataObject $formData
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return AbstractBlock|null
     */
    public function getSpecialBlockForAttribute($attribute, $formData);


    /**
     * Return true if attribut has special block
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return bool
     */
    public function hasSpecialBlockForAttribute($attribute);
}
