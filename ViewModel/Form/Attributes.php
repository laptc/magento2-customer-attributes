<?php

/**
 * Created on Sat Mar 04 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\ViewModel\Form;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Tangkoko\CustomerAttributesManagement\Model\Attribute\ProviderInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;
use Tangkoko\CustomerAttributesManagement\Model\Form\DataResolverInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Attributes implements ArgumentInterface
{

    protected ProviderInterface $attributeProvider;

    /**
     *
     * @var Converter
     */
    protected $converter;

    /**
     *
     * @var DataResolverInterface
     */
    protected DataResolverInterface $dataResolver;

    protected ?ArgumentInterface $attributeViewModel;


    /**
     * Attributes constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     */
    public function __construct(
        ProviderInterface $attributeProvider,
        Converter $converter,
        DataResolverInterface $dataResolver,
        ?ArgumentInterface $attributeViewModel = null
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->converter = $converter;
        $this->dataResolver = $dataResolver;
        $this->attributeViewModel = $attributeViewModel;
    }



    /**
     * return attributes
     *
     * @return \Magento\Eav\Model\Attribute[]
     */
    public function getAttributes(string $formCode): array
    {
        $attributes = [];
        foreach ($this->attributeProvider->getAttributes($formCode) as  $attribute) {
            if ($attribute->getIsVisible() && $attribute->getExtensionAttributes()->getCamAttribute() && $attribute->getExtensionAttributes()->getCamAttribute()->isVisible($this->getFormData())) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $attributes;
    }



    /**
     * Return true if attribute is in fieldset
     *
     * @return ?ArgumentInterface
     */
    public function getAttributeViewModel(): ?ArgumentInterface
    {
        return $this->attributeViewModel;
    }



    /**
     * Return rules configuration
     *
     * @return array
     */
    public function getRules(string $formCode): array
    {
        $rules = [];
        foreach ($this->getAttributes($formCode) as $attributeModel) {
            if ($attributeModel->getExtensionAttributes()->getCamAttribute()->getVisibilityConditions()->getConditions()) {
                $rules[$attributeModel->getAttributeCode()] = $this->converter->dataModelToArray($attributeModel->getExtensionAttributes()->getCamAttribute()->getVisibilityConditions());
            }
        }
        return $rules;
    }

    /**
     * Return form data
     *
     * @return AbstractModel
     */
    public function getFormData(): AbstractModel
    {
        return $this->dataResolver->getFormData();
    }
}
