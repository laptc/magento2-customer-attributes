<?php

namespace Tangkoko\CustomerAttributesManagement\Block\Address\Attributes;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;
use Tangkoko\CustomerAttributesManagement\Block\Customer\Attributes\SpecialBlockProviderInterface;
use Magento\Customer\ViewModel\Address as AddressViewModel;

class SpecialBlockProvider implements SpecialBlockProviderInterface
{
    /**
     *
     * @var AbstractBlock[]
     */
    private $specialBlocks;

    /**
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     *
     * @var AddressViewModel
     */
    private $addressViewModel;

    /**
     * constructor
     *
     * @param array $specialBlock
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        AddressViewModel $addressViewModel,
        $specialBlocks = []
    ) {
        $this->specialBlocks = $specialBlocks;
        $this->layout = $context->getLayout();
        $this->addressViewModel = $addressViewModel;
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
                    ->setData("attribute", $attribute)
                    ->setObject($formData)
                    ->setHtmlClass($attribute->getFrontendClass());

                break;
            case "prefix":
                break;
            case "firstname":
                break;
            case "region":
                break;
            case "region_id":
                $block = $this->getLayout()->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setData("attribute", $attribute)
                    ->setData("view_model", $this->addressViewModel)
                    ->setFormData($formData)
                    ->setRegion("region", $formData->getRegion())
                    ->setData($attribute->getAttributeCode(), $formData->getData($attribute->getAttributeCode()))
                    ->setHtmlClass($attribute->getFrontendClass());
                break;
            default:
                $block = $this->getLayout()->createBlock($this->specialBlocks[$attribute->getAttributeCode()])
                    ->setData("attribute", $attribute)
                    ->setData("view_model", $this->addressViewModel)
                    ->setFormData($formData)
                    ->setData($attribute->getAttributeCode(), $formData->getData($attribute->getAttributeCode()))
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
