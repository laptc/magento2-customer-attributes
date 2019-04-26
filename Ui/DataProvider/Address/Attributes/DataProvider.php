<?php
/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mvn\Cam\Ui\DataProvider\Address\Attributes;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Store\Model\Store;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\AttributeFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as OptionCollectionFactory;

/**
 * Class DataProvider
 * @package Mvn\Cam\Ui\DataProvider\Address\Attributes
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var OptionCollectionFactory
     */
    protected $attrOptionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Attribute
     */
    protected $attribute;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param ArrayManager $arrayManager
     * @param RequestInterface $request
     * @param AttributeFactory $attributeFactory
     * @param OptionCollectionFactory $attrOptionCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreRepositoryInterface $storeRepository,
        ArrayManager $arrayManager,
        RequestInterface $request,
        AttributeFactory $attributeFactory,
        OptionCollectionFactory $attrOptionCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->storeRepository = $storeRepository;
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->attributeFactory = $attributeFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
    }

    /**
     * @return \Magento\Customer\Model\Attribute
     */
    public function getAttribute(){
        if(!$this->attribute){
            $attribute = $this->attributeFactory->create();
            $attributeId = $this->request->getParam('attribute_id');
            if($attributeId) {
                $attribute->load($attributeId);
            }
            $this->attribute = $attribute;
        }
        return $this->attribute;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        $attribute = $this->getAttribute();
        if($attribute->getId()){
            $inputType = $attribute->getFrontendInput();
            if(empty($attribute->getData("frontend_label[0]")) && !empty($attribute->getData("frontend_label"))){
                $attribute->setData("frontend_label[0]", $attribute->getData("frontend_label"));
            }
            $labels = $attribute->getStoreLabels();
            if($labels && !empty($labels)){
                foreach ($labels as $storeId => $label){
                    $attribute->setData("frontend_label[$storeId]", $label);
                }
            }

            if($attribute->usesSource()){
                $optionsData = [];
                foreach ($this->storeRepository->getList() as $store) {
                    $storeId = $store->getId();
                    if (!$storeId) {
                        continue;
                    }
                    $options = $this->getAttributeOptions($attribute->getId(), $storeId);
                    if($options && !empty($options)){
                        $attributeDefaultValue = explode(",", $attribute->getDefaultValue());
                        foreach ($options as $option) {
                            $optionId = $option->getOptionId();
                            if(isset($optionsData[$optionId])){
                                $optionsData[$optionId]["value_option_$storeId"] = $option->getValue();
                            }else{
                                $optionsData[$optionId] = [
                                    "record_id" => $optionId,
                                    "option_id" => $optionId,
                                    "is_default" => (in_array($optionId, $attributeDefaultValue))?1:0,
                                    "position" => $option->getSortOrder(),
                                    "value_option_0" => $option->getDefaultValue(),
                                    "value_option_$storeId" => $option->getValue()
                                ];
                            }
                        }
                    }
                }
                $attribute->setData("attribute_options_$inputType", array_values($optionsData));
            }

            $defaultValueField = $attribute->getDefaultValueByInput($inputType);
            if ($defaultValueField) {
                $attribute->setData($defaultValueField, $attribute->getDefaultValue());
            }

            $attribute->setUsedInForms($attribute->getUsedInForms());
            $data[""] = $attribute->getData();
        }

        return $data;
    }

    /**
     * Get meta information
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->customizeBase($meta);
        $meta = $this->customizeFrontendLabels($meta);
        $meta = $this->customizeOptions($meta);
        return $meta;
    }

    /**
     * Customize base field
     *
     * @param array $meta
     * @return array
     */
    private function customizeBase($meta)
    {
        $attribute = $this->getAttribute();
        $disabled = ($attribute->getId())?true:false;
        $childrens = $this->arrayManager->set(
            'frontend_input/arguments/data/config',
            [],
            [
                'disabled' => $disabled
            ]
        );
        $meta['base_fieldset']['children'] = $this->arrayManager->set(
            'attribute_code/arguments/data/config',
            $childrens,
            [
                'disabled' => $disabled,
                'notice' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'validation' => [
                    'max_text_length' => EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ]
            ]
        );
        return $meta;
    }

    /**
     * Customize frontend labels
     *
     * @param array $meta
     * @return array
     */
    private function customizeFrontendLabels($meta)
    {
        $labelConfigs = [];

        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();

            if (!$storeId) {
                continue;
            }
            $labelConfigs['frontend_label[' . $storeId . ']'] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                [
                    'formElement' => Input::NAME,
                    'componentType' => Field::NAME,
                    'label' => $store->getName(),
                    'dataType' => Text::NAME,
                    'dataScope' => 'frontend_label[' . $storeId . ']'
                ]
            );
        }
        $meta['manage-titles']['children'] = $labelConfigs;

        return $meta;
    }

    /**
     * Customize options
     *
     * @param array $meta
     * @return array
     */
    private function customizeOptions($meta)
    {
        $sortOrder = 1;
        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();
            $storeLabelConfiguration = [
                'dataType' => 'text',
                'formElement' => 'input',
                'component' => 'Mvn_Cam/js/form/element/input',
                'template' => 'Magento_Catalog/form/element/input',
                'prefixName' => 'option.value',
                'prefixElementName' => 'option_',
                'suffixName' => (string)$storeId,
                'label' => $store->getName(),
                'sortOrder' => $sortOrder,
                'componentType' => Field::NAME,
            ];
            // JS code can't understand 'required-entry' => false|null, we have to avoid even empty property.
            if ($store->getCode() == Store::ADMIN_CODE) {
                $storeLabelConfiguration['validation'] = [
                    'required-entry' => true,
                ];
            }
            $meta['attribute_options_select_container']['children']['attribute_options_select']['children']
            ['record']['children']['value_option_' . $storeId] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                $storeLabelConfiguration
            );

            $meta['attribute_options_multiselect_container']['children']['attribute_options_multiselect']['children']
            ['record']['children']['value_option_' . $storeId] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                $storeLabelConfiguration
            );
            ++$sortOrder;
        }

        $meta['attribute_options_select_container']['children']['attribute_options_select']['children']
        ['record']['children']['action_delete'] = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'componentType' => 'actionDelete',
                'dataType' => 'text',
                'fit' => true,
                'sortOrder' => $sortOrder,
                'component' => 'Mvn_Cam/js/form/element/action-delete',
                'elementTmpl' => 'Mvn_Cam/form/element/action-delete',
                'template' => 'Mvn_Cam/form/element/action-delete',
                'prefixName' => 'option.delete',
                'prefixElementName' => 'option_',
            ]
        );
        $meta['attribute_options_multiselect_container']['children']['attribute_options_multiselect']['children']
        ['record']['children']['action_delete'] = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'componentType' => 'actionDelete',
                'dataType' => 'text',
                'fit' => true,
                'sortOrder' => $sortOrder,
                'component' => 'Mvn_Cam/js/form/element/action-delete',
                'elementTmpl' => 'Mvn_Cam/form/element/action-delete',
                'template' => 'Mvn_Cam/form/element/action-delete',
                'prefixName' => 'option.delete',
                'prefixElementName' => 'option_',
            ]
        );
        return $meta;
    }

    /**
     * @param $attributeId
     * @param $storeId
     * @return mixed
     */
    public function getAttributeOptions($attributeId, $storeId)
    {
        $options = $this->attrOptionCollectionFactory->create()
            ->setPositionOrder('asc')
            ->setAttributeFilter($attributeId)
            ->setStoreFilter($storeId)
            ->load();
        return $options;
    }

}
