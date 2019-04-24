<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Block\Customer\Form;

/**
 * Class Attributes
 * @package Mvn\Cam\Block\Customer\Form
 */
class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var string
     */
    protected $code = "";

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Mvn_Cam::customer/form/default.phtml";

    /**
     * Attributes constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerMetadata = $customerMetadata;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setFormCode($code){
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormCode(){
        return $this->code;
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormAttributes(){
        return $this->customerMetadata->getAttributes($this->getFormCode());
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        if($this->getFormCode()){
            $attributes = $this->getFormAttributes();
            if(!empty($attributes)){
                foreach ($attributes as $attribute){
                    if($attribute->isVisible() && $attribute->isUserDefined()){
                        $this->addChild($attribute->getAttributeCode(), $this->getBlockForAttribute($attribute->getFrontendInput()), [
                            'attribute' => $attribute
                        ]);
                    }
                }
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * @param string $inputType
     * @return string
     */
    public function getBlockForAttribute($inputType){
        $blockName = "AbstractElement";
        if($inputType){
            $blockNames = [];
            foreach (explode('_', $inputType) as $name) {
                $blockNames[] = ucfirst($name);
            };
            $blockName = implode("", $blockNames);
        }
        return "Mvn\Cam\Block\Attributes\\$blockName";
    }
}
