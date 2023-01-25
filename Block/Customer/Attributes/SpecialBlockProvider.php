<?php

namespace Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;

class SpecialBlockProvider implements SpecialBlockProviderInterface
{
    private $specialBlocks;

    private $layout;

    /**
     * constructor
     *
     * @param array $specialBlock
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        $specialBlocks = []
    ) {
        $this->specialBlocks = $specialBlocks;
        $this->layout = $context->getLayout();
    }


    /**
     * Return true if attribut has special block
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return bool
     */
    public function hasSpecialBlockForAttribute($attribute)
    {
        return isset($this->specialBlocks[$attribute->getAttributeCode()]);
    }

    /**
     * Return Block
     *
     * @param DataObject $formData
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @return AbstractBlock|null
     */
    public function getSpecialBlockForAttribute($attribute, $formData)
    {
        $block = null;
        switch ($attribute->getAttributeCode()) {
            case "lastname":
                $block = $this->getLayout()
                    ->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setObject($formData)
                    ->setForceUseCustomerAttributes(true)
                    ->setHtmlClass($attribute->getFrontendClass());
                break;
            case "dob":
                $block = $this->getLayout()
                    ->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setDate($formData->getDob())
                    ->setHtmlClass($attribute->getFrontendClass());
                break;
            case "taxvat":
                $block = $this->getLayout()->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setTaxvat($formData->getTaxvat())
                    ->setHtmlClass($attribute->getFrontendClass());
                break;
            case "gender":
                $block = $this->getLayout()->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setGender($formData->getGender())
                    ->setHtmlClass($attribute->getFrontendClass());
                break;
            case "firstname":
                break;
            case "email":
                break;
            default:
                $block = $this->getLayout()->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setAttribute($attribute)
                    ->setFormData($formData)
                    ->setHtmlClass($attribute->getFrontendClass());
        }

        return $block;
    }

    /**
     * Retrieve layout object
     *
     * @return \Magento\Framework\View\LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getLayout()
    {
        if (!$this->layout) {
            throw new \Magento\Framework\Exception\LocalizedException(
                new \Magento\Framework\Phrase('Layout must be initialized')
            );
        }
        return $this->layout;
    }
}
