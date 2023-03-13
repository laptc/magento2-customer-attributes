<?php

/**
 * Created on Mon Mar 13 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tangkoko\CustomerAttributesManagement\Helper\Data;

class Address implements ArgumentInterface
{
    private \Magento\Customer\ViewModel\Address $addressViewModel;

    private Data $configHelper;
    /**
     * Constructor
     *
     * @param \Magento\Customer\ViewModel\Address $addressViewModel
     */
    public function __construct(
        \Magento\Customer\ViewModel\Address $addressViewModel,
        Data $configHelper
    ) {
        $this->addressViewModel = $addressViewModel;
        $this->configHelper = $configHelper;
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param string $attributeCode
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function dataGetAttributeValidationClass($attributeCode)
    {
        return $this->addressViewModel->dataGetAttributeValidationClass($attributeCode);
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param string $attributeCode
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addressGetAttributeValidationClass($attributeCode)
    {
        return $this->addressViewModel->addressGetAttributeValidationClass($attributeCode);
    }

    /**
     * Return Number of Lines in a Street Address for store
     *
     * @param \Magento\Store\Model\Store|int|string $store
     *
     * @return int
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addressGetStreetLines()
    {
        return $this->addressViewModel->addressGetStreetLines();
    }

    /**
     * Check if VAT ID address attribute has to be shown on frontend (on Customer Address management forms)
     *
     * @return boolean
     */
    public function addressIsVatAttributeVisible()
    {
        return $this->addressViewModel->addressIsVatAttributeVisible();
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function dataGetRegionJson()
    {
        return $this->addressViewModel->dataGetRegionJson();
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @param bool $asJson
     * @return array|string
     */
    public function dataGetCountriesWithOptionalZip($asJson)
    {
        return $this->addressViewModel->dataGetCountriesWithOptionalZip($asJson);
    }

    public function addressGetStreetLinesPlaceholder(int $i): string
    {
        $placeholders =  $this->configHelper->getStreetPlaceholders();
        return isset($placeholders[$i]) ? $placeholders[$i] : "";
    }
}
