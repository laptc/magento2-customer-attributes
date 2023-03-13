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

    private \Magento\Eav\Model\Config $eavConfig;

    /**
     * Constructor
     *
     * @param array|null $entityTypeFieldsets
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        ?array $entityTypeFieldsets = []
    ) {
        $this->entityTypeFieldsets = $entityTypeFieldsets;
        $this->eavConfig = $eavConfig;
    }

    /**
     * return default attribute fieldset
     *
     * @param AttributeInterface $attribute
     * @return void
     */
    public function getDefaultFieldset(AttributeInterface  $attribute): ?string
    {
        $code = $this->eavConfig->getEntityType($attribute->getEntityTypeId())->getEntityTypeCode();
        if (isset($this->entityTypeFieldsets[$code])) {
            $fieldsets = $this->entityTypeFieldsets[$code]->toOptionArray();
            return $fieldsets[0]["value"];
        }
        return null;
    }
}
