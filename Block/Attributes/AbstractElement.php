<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Block\Attributes;

/**
 * Class AbstractElement
 * @package Mvn\Cam\Block\Attributes
 */
class AbstractElement extends \Magento\Framework\View\Element\Template
{
    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     */
    public function getAttribute(){
        return parent::getAttribute();
    }

    /**
     * @return string
     */
    public function getAttributeValue(){
        $value = "";
        $data = $this->getFormData();
        $attribute = $this->getAttribute();
        if($attribute){
            if($data instanceof \Magento\Framework\DataObject){
                $value = $data->getData($attribute->getAttributeCode());
            }else{
                $value = $this->getDefaultValue();
            }
        }
        return $value;
    }
}
