<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Form;

use Magento\Framework\View\Element\BlockInterface;
use Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes\SpecialBlockProviderInterface;

/**
 * Class Attributes
 * @package Tangkoko\CustomerAttributesManagement\Block\Customer\Form
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
    protected $metadata;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var string
     */
    protected $code = "";

    /**
     *
     * @var SpecialBlockProviderInterface
     */
    protected $specialBlockProvider;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Tangkoko_CustomerAttributesManagement::customer/form/default.phtml";

    protected $attributes;


    /**
     * Attributes constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\MetadataInterface $metadata,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        SpecialBlockProviderInterface $specialBlockProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->metadata = $metadata;
        $this->attributeFactory = $attributeFactory;
        $this->specialBlockProvider = $specialBlockProvider;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setFormCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormCode()
    {
        return $this->code;
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormAttributes()
    {
        return $this->metadata->getAttributes($this->getFormCode());
    }

    /**
     * Return attributes
     *
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        if ($this->getFormCode()) {
            $this->attributes = $this->getFormAttributes();
            if (!empty($this->attributes)) {
                usort($this->attributes, function ($attribute1, $attribute2) {
                    return $attribute1->getSortOrder() <=> $attribute2->getSortOrder();
                });

                foreach ($this->attributes as $index => $attribute) {
                    if ($attribute->isVisible() && $this->isInFielsdset($attribute)) {
                        if ($attribute->isUserDefined() && !$this->specialBlockProvider->hasSpecialBlockForAttribute($attribute)) {
                            $block = $this->getBlockForAttribute($attribute);
                        } else {
                            $block = $this->specialBlockProvider->getSpecialBlockForAttribute($attribute, $this->getFormData());
                        }
                        if ($block) {
                            $this->setChild($this->getNameInLayout() . '.' .  $attribute->getAttributeCode(), $block);
                        }
                    }
                }
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Return true if attribute is in fieldset
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return boolean
     */
    public function isInFielsdset($attribute)
    {
        return true;
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return BlockInterface
     */
    public function getBlockForAttribute($attribute)
    {
        $blockName = "Text";

        $blockNames = [];
        foreach (explode('_', $attribute->getFrontendInput()) as $name) {
            $blockNames[] = ucfirst($name);
        };
        $blockName = implode("", $blockNames);
        $block = $this->getLayout()
            ->createBlock(
                "Tangkoko\CustomerAttributesManagement\Block\Attributes\\" . $blockName,
                $attribute->getAttributeCode(),
                [
                    "data" => [
                        'attribute' => $attribute,
                        'form_data' => $this->getFormData(),
                        'default_value' => $this->getDefaultValue($attribute)
                    ]
                ]
            );
        return $block;
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {

        $data = $this->getData('form_data');
        if ($data === null) {
            if ($this->customerSession->getCustomerData()) {
                $formData = $this->customerSession->getCustomerData()->__toArray();
                foreach ($formData["custom_attributes"] as $customAttribute) {
                    $formData[$customAttribute["attribute_code"]] = $customAttribute["value"];
                }
            } else {
                $formData = $this->customerSession->getCustomerFormData(true);
            }

            $data = new \Magento\Framework\DataObject();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
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
    public function getDefaultValue($attribute)
    {
        $defaultValue = "";
        if ($attribute) {
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
