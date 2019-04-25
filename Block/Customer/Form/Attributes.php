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
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $attributeFactory;

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
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerMetadata = $customerMetadata;
        $this->attributeFactory = $attributeFactory;
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
                usort($attributes, function ($attribute1, $attribute2) {
                    return $attribute1->getSortOrder() <=> $attribute2->getSortOrder();
                });
                foreach ($attributes as $attribute){
                    if($attribute->isVisible() && $attribute->isUserDefined()){
                        $this->addChild($attribute->getAttributeCode(), $this->getBlockForAttribute($attribute->getFrontendInput()), [
                            'attribute' => $attribute,
                            'form_data' => $this->getFormData(),
                            'default_value' => $this->getDefaultValue($attribute)
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
        $blockName = "Text";
        if($inputType){
            $blockNames = [];
            foreach (explode('_', $inputType) as $name) {
                $blockNames[] = ucfirst($name);
            };
            $blockName = implode("", $blockNames);
        }
        return "Mvn\Cam\Block\Attributes\\$blockName";
    }

    /**
     * @return array|mixed|null
     */
    public function getFormData(){
        $data = $this->getData('form_data');
        if ($data === null) {
            $customer = $this->customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data = $customer;
            }else{
                $formData = $this->customerSession->getCustomerFormData(true);
                if ($formData) {
                    $data = new \Magento\Framework\DataObject();
                    $data->addData($formData);
                    $data->setCustomerData(1);
                }
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultValue($attribute){
        $defaultValue = "";
        if($attribute){
            /**
             * @var \Magento\Customer\Model\Attribute $attributeModel
             */
            $attributeModel = $this->attributeFactory->create();
            $attributeModel->loadByCode(\Magento\Customer\Model\Customer::ENTITY, $attribute->getAttributeCode());
            $defaultValue = $attributeModel->getDefaultValue();
        }
        return $defaultValue;
    }
}
