<?php

/**
 * Created on Sat Mar 04 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Attribute;

use Magento\Customer\Model\AttributeMetadataDataProvider;

class Provider implements ProviderInterface
{

    protected AttributeMetadataDataProvider $attributeMetadataProvider;

    protected string $entityType;

    protected ?array $attributes = null;

    /**
     * Constructor
     *
     * @param AttributeMetadataDataProvider $attributeMetadataProvider
     */
    public  function __construct(
        AttributeMetadataDataProvider $attributeMetadataProvider,
        string $entityType
    ) {
        $this->attributeMetadataProvider = $attributeMetadataProvider;
        $this->entityType = $entityType;
    }


    /**
     * Return form attributes
     *
     * @return \Magento\Eav\Model\Attribute[]
     */
    public function getAttributes(string $formCode): array
    {
        if (!$this->attributes) {
            $this->attributes = [];

            $attributes = $this->attributeMetadataProvider->loadAttributesCollection($this->entityType, $formCode);
            if (!empty($attributes)) {
                $items = $attributes->getItems();
                usort($items, function (\Magento\Eav\Model\Attribute $attribute1, \Magento\Eav\Model\Attribute $attribute2) {
                    return $attribute1->getSortOrder() <=> $attribute2->getSortOrder();
                });
            }
            $this->attributes = $items;
        }
        return $this->attributes;
    }
}
