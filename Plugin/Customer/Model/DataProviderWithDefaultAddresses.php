<?php

/**
 * Created on Tue Nov 21 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Customer\Model;

use Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses as Subject;

class DataProviderWithDefaultAddresses
{

    /**
     *
     * @var \Magento\Eav\Model\Config
     */
    private \Magento\Eav\Model\Config $eavConfig;

    public function __construct(\Magento\Eav\Model\Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }
    /**
     * Get attributes meta
     *
     * @param Type $entityType
     * @return array
     * @throws LocalizedException
     */
    public function afterGetMeta(Subject $subject, ?array $meta = []): array
    {

        $attributes = $this->eavConfig->getEntityAttributes("customer");


        /** @var AbstractAttribute $attribute */
        foreach ($attributes as $attribute) {

            if ($attribute->getExtensionAttributes()->getCamAttribute()) {
                /**
                 * @var \Tangkoko\CustomerAttributesManagement\Model\Data\CamAttribute
                 */
                $camAttribute = $attribute->getExtensionAttributes()->getCamAttribute();
                $meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["required"] = (bool)$meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["required"]
                    && count($camAttribute->getConditions()->getConditions()) == 0
                    && count($camAttribute->getRequiredConditions()->getConditions()) == 0;
                if (isset($meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["validation"])) {
                    $validation = $meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["validation"];
                    if (isset($validation) && isset($validation["required-entry"])) {
                        $meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["validation"]["required-entry"] =
                            (bool)$meta["customer"]["children"][$attribute->getAttributeCode()]["arguments"]["data"]["config"]["validation"]["required-entry"]
                            && count($camAttribute->getConditions()->getConditions()) == 0
                            && count($camAttribute->getRequiredConditions()->getConditions()) == 0;
                    }
                }
            }
        }
        return $meta;
    }
}
