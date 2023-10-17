<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Model\Rule\Condition;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Address rule condition data model.
 */
class Store extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param StoreManagerInterface $storeManagerInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $options = ["store" => __("Store")];
        $this->setAttributeOption($options);
        return $this;
    }

    /**
     * Get attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__store',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][store]',
                'values' => $this->getAttributeSelectOptions(),
                'value' => "Store",
                'value_name' => "Store",
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
            $this->_layout->getBlockSingleton(\Magento\Rule\Block\Editable::class)
        );
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }



    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'multiselect';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {

        return 'multiselect';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $options = [];
            foreach ($this->storeManager->getStores() as $store) {
                $options[] = ["value" => $store->getId(), "label" =>  $store->getName()];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        return parent::validate($model);
    }



    /**
     * Validate product attribute value for condition
     *
     * @param   object|array|int|string|float|bool|null $validatedValue product attribute value
     * @return  bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function isVisible($validatedValue)
    {
        return $this->validateAttribute($this->storeManager->getStore()->getId());
    }
}
