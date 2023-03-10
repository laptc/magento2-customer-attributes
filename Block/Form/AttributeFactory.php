<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Block\Form;

use \Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;

class AttributeFactory
{


    /**
     * 
     *
     * @var AbstractBlock[]
     */
    private array $attributeBlocks;

    private LayoutInterface $layout;

    /**
     *
     * @param LayoutInterface $layout
     * @param array|null $attributeBlocks
     */
    public function __construct(
        LayoutInterface $layout,
        ?array $attributeBlocks = []
    ) {
        $this->attributeBlocks = $attributeBlocks;
        $this->layout = $layout;
    }

    public function create(AttributeInterface $attribute, \Magento\Framework\DataObject $object): BlockInterface
    {
        if (isset($this->attributeBlocks[$attribute->getAttributeCode()])) {
            $blockType = $this->attributeBlocks[$attribute->getAttributeCode()];
        } else {
            $blockType = $this->getBlockTypeForAttribute($attribute);
        }

        $block = $this->layout
            ->createBlock(
                $blockType,
                $attribute->getAttributeCode(),
                [
                    "data" => [
                        'attribute' => $attribute,
                        'object' => $object,
                        $attribute->getAttributeCode() => $object->getData($attribute->getAttributeCode()),
                        'default_value' => $attribute->getDefaultValue()
                    ]
                ]
            );

        return $block;
    }

    /**
     * @param AttributeInterface $attribute
     * @return string
     */
    private function getBlockTypeForAttribute(AttributeInterface $attribute): string
    {
        $blockNames = [];
        foreach (explode('_', $attribute->getFrontendInput()) as $name) {
            $blockNames[] = ucfirst($name);
        };
        $blockName = implode("", $blockNames);
        return "Tangkoko\CustomerAttributesManagement\Block\Attributes\\" . $blockName;
    }
}
