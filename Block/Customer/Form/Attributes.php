<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Form;

use Elasticsearch\Serializers\ArrayToJSONSerializer;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Framework\View\Element\BlockInterface;
use Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes\SpecialBlockProviderInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;

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
     *
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     *
     * @var AttributeInterface[]
     */
    protected $attributeModels;

    /**
     *
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     *
     * @var Converter
     */
    protected $converter;

    /**
     *
     * @var Json
     */
    private $serializer;

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
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Converter $converter,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->metadata = $metadata;
        $this->attributeFactory = $attributeFactory;
        $this->specialBlockProvider = $specialBlockProvider;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->converter = $converter;
        $this->serializer = $serializer;
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
     * return attribute
     *
     * @param string $code
     * @return AttributeInterface
     */
    protected function getAttributeModel(string $code)
    {
        if (!$this->attributeModels) {
            $this->attributeModels = [];
            $models = $this->attributeRepository->getList(\Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $this->searchCriteriaBuilderFactory->create()->create());
            foreach ($models->getItems() as $attributeModel) {
                $this->attributeModels[$attributeModel->getAttributeCode()] = $attributeModel;
            }
        }
        return $this->attributeModels[$code];
    }

    /**
     * return attributes
     *
     * @return AttributeMetadataInterface[]
     */
    public function getAttributes()
    {
        if (!$this->attributes) {
            $this->attributes = [];
            $attributes = $this->getFormAttributes();
            if (!empty($attributes)) {
                usort($attributes, function ($attribute1, $attribute2) {
                    return $attribute1->getSortOrder() <=> $attribute2->getSortOrder();
                });

                foreach ($attributes as $index => $attribute) {
                    if ($attribute->isVisible() && $this->isInFielsdset($attribute) && $this->getAttributeModel($attribute->getAttributeCode())->getExtensionAttributes()->getCamAttribute()->isVisible($this->customerSession->getCustomer())) {
                        $this->attributes[] = $attribute;
                    }
                }
            }
        }
        return $this->attributes;
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {

        if ($this->getFormCode()) {
            foreach ($this->getAttributes() as $index => $attribute) {
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
            $defaultValue = $this->getAttributeModel($attribute->getAttributeCode())->getDefaultValue();
        }

        return $defaultValue;
    }


    /**
     * Return attribute configuration json encoded
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $rules = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributeModel = $this->getAttributeModel($attribute->getAttributeCode());
            if ($attributeModel->getExtensionAttributes()->getCamAttribute()->getVisibilityConditions()->getConditions()) {
                $rules[$attribute->getAttributeCode()] = $this->converter->dataModelToArray($attributeModel->getExtensionAttributes()->getCamAttribute()->getVisibilityConditions());
            }
        }


        return $this->serializer->serialize(
            ["conditions" => $rules, "model" => $this->customerSession->getCustomer()->toJson()]
        );
    }
}
