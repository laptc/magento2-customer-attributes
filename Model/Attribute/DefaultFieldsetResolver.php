<?php

/**
 * Created on Mon Mar 13 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Model\Attribute;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Data\OptionSourceInterface;

class DefaultFieldsetResolver
{

    /**
     * Undocumented function
     *
     * @param OptionSourceInterface[] $entityTypeFieldsets
     */
    private $entityTypeFieldsets = [];

    /**
     * Constructor
     *
     * @param array|null $entityTypeFieldsets
     */
    public function __construct(
        ?array $entityTypeFieldsets = []
    ) {
        $this->entityTypeFieldsets = $entityTypeFieldsets;
    }

    /**
     * return default attribute fieldset
     *
     * @param AttributeInterface $attribute
     * @return void
     */
    public function getDefaultFieldset(AttributeInterface  $attribute): ?string
    {
        if (isset($this->entityTypeFieldsets[$attribute->getEntityTypeId()])) {
            $fieldsets = $this->entityTypeFieldsets[$attribute->getEntityTypeId()]->toOptionArray();
            return $fieldsets[0]["value"];
        }
        return null;
    }
}
