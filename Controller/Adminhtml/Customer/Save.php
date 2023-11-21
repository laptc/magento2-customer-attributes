<?php

/**
 *
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\View\LayoutFactory;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\EntityFactory;
use Magento\Eav\Model\AttributeFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;

/**
 * Product attribute save controller.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Customer\Attribute implements HttpPostActionInterface
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var FormData|null
     */
    private $formDataSerializer;

    private $camAttributeFactory;

    private Converter $converter;

    private Json $json;


    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tangkoko\CustomerAttributesManagement\Helper\Data $helper
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param ValidatorFactory $validatorFactory
     * @param CollectionFactory $groupCollectionFactory
     * @param FilterManager $filterManager
     * @param LayoutFactory $layoutFactory
     * @param \Magento\Eav\Model\AttributeFactory $attributeFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param EntityFactory $entityFactory
     * @param CamAttributeFactory $camAttributeFactory
     * @param Converter $converter
     * @param Json $json
     * @param FormData|null $formDataSerializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tangkoko\CustomerAttributesManagement\Helper\Data $helper,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Product $productHelper,
        ValidatorFactory $validatorFactory,
        CollectionFactory $groupCollectionFactory,
        FilterManager $filterManager,
        LayoutFactory $layoutFactory,
        \Magento\Eav\Model\AttributeFactory $attributeFactory,
        AttributeRepositoryInterface $attributeRepository,
        EntityFactory $entityFactory,
        CamAttributeFactory $camAttributeFactory,
        Converter $converter,
        Json $json,
        FormData $formDataSerializer = null
    ) {
        parent::__construct($context, $helper, $attributeLabelCache, $coreRegistry, $attributeFactory, $attributeRepository, $entityFactory);
        $this->productHelper = $productHelper;
        $this->filterManager = $filterManager;
        $this->attributeFactory = $attributeFactory;
        $this->validatorFactory = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->layoutFactory = $layoutFactory;
        $this->camAttributeFactory = $camAttributeFactory;
        $this->json = $json;
        $this->converter = $converter;
        $this->formDataSerializer = $formDataSerializer
            ?: ObjectManager::getInstance()->get(FormData::class);
    }

    /**
     * @inheritdoc
     *
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        try {
            $optionData = $this->formDataSerializer
                ->unserialize($this->getRequest()->getParam('serialized_options', '[]'));
        } catch (\InvalidArgumentException $e) {
            $message = __("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);
            return $this->returnResult('cam/*/edit', ['_current' => true], ['error' => true]);
        }

        $data = $this->getRequest()->getPostValue();

        $data = array_replace_recursive(
            $data,
            $optionData
        );

        if ($data) {
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            /**
             * @var \Magento\Customer\Model\Attribute $model
             */
            $model = $this->attributeFactory->createAttribute(\Magento\Customer\Model\Attribute::class);
            if ($attributeCode) {
                try {
                    $model =  $this->attributeRepository->get(\Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributeCode);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                }
            }
            $attributeId = $model->getId();
            if (strlen($attributeCode) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(
                    ['pattern' => '/^[a-zA-Z\x{600}-\x{6FF}][a-zA-Z\x{600}-\x{6FF}_0-9]{0,30}$/u']
                );
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z or A-Z), ' .
                                'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );
                    return $this->returnResult(
                        'cam/*/edit',
                        ['attribute_code' => $attributeCode, '_current' => true],
                        ['error' => true]
                    );
                }
            }

            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var Validator $inputType */
                $inputType = $this->validatorFactory->create();
                if (!$inputType->isValid($data['frontend_input'])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addErrorMessage($message);
                    }
                    return $this->returnResult(
                        'cam/*/edit',
                        ['attribute_code' => $attributeCode, '_current' => true],
                        ['error' => true]
                    );
                }
            }

            if ($attributeCode) {
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                    return $this->returnResult('cam/*/', [], ['error' => true]);
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->entityTypeId) {
                    $this->messageManager->addErrorMessage(__('We can\'t update the attribute.'));
                    $this->_session->setAttributeData($data);
                    return $this->returnResult('cam/*/', [], ['error' => true]);
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $data['frontend_input'] ?? $model->getFrontendInput();
            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType(
                    $data['frontend_input']
                );
                $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );

                if ($data['frontend_input'] == "multiselect") {
                    $data['source_model'] = \Magento\Eav\Model\Entity\Attribute\Source\Table::class;
                }

                if ($model->getIsUserDefined() === null) {
                    $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
                }
            }


            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            $data['is_visible_in_grid'] = ((bool) $data['is_used_in_grid']) ? 1 : 0;
            $data['is_searchable_in_grid'] = ((bool) $data['is_filterable_in_grid']) ? 1 : 0;

            if (!empty($data['option']['delete'])) {
                $data['option']['value'] = (isset($data['option']['value'])) ? $data['option']['value'] : [];
                foreach ($data['option']['delete'] as $id => $isDeleted) {
                    if ($isDeleted) {
                        $data['option']['value'][$id] = $isDeleted;
                    }
                }
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (isset($data['default']) && is_array($data['default'])) {
                $data['default_value'] = implode(',', $data['default']);
            }

            $scopeDataFields = ["is_visible", "is_required", "default_value", "multiline_count"];
            foreach ($scopeDataFields as $fieldName) {
                if (isset($data[$fieldName])) {
                    $data["scope_$fieldName"] = $data[$fieldName];
                }
            }
            $model->addData($data);

            if (isset($data["validate_rules"]) && !empty($data["validate_rules"])) {
                $model->setValidateRules([$data["validate_rules"] => true]);
            } else {
                $model->setValidateRules(null);
            }


            if (!$model->getAttributeSetId()) {
                $model->setAttributeSetId(CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
                $model->setAttributeGroupId(CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
            }

            if (!$attributeId) {
                $model->setEntityTypeId($this->entityTypeId);
                $model->setIsUserDefined(1);
            }

            $camAttribute = $model->getExtensionAttributes()->getCamAttribute();
            if (!$camAttribute) {
                $camAttribute = $this->camAttributeFactory->create();
            }
            $camAttribute->setAttributeId($model->getAttributeId());
            $camAttribute->loadPost($data);


            if (isset($data['rule']['required_conditions'])) {
                $camAttribute->setRequiredConditionsSerialized($this->json->serialize($this->converter->dataModelToArray($camAttribute->getRequiredConditions())));
            } else {
                $camAttribute->setRequiredConditionsSerialized($this->json->serialize([]));
            }
            if (isset($data['rule']['conditions'])) {
                $camAttribute->setVisibilityConditionsSerialized($this->json->serialize($this->converter->dataModelToArray($camAttribute->getVisibilityConditions())));
            } else {
                $camAttribute->setVisibilityConditionsSerialized($this->json->serialize([]));
            }


            $model->getExtensionAttributes()->setCamAttribute($camAttribute);

            try {
                $model->save();
                $this->attributeRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the customer attribute.'));

                $this->attributeLabelCache->clean();
                $this->_session->setAttributeData(false);
                return $this->returnResult(
                    'cam/*/edit',
                    ['attribute_code' => $attributeCode, '_current' => true],
                    ['error' => false]
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setAttributeData($data);
                return $this->returnResult(
                    'cam/*/edit',
                    ['attribute_code' => $attributeCode, '_current' => true],
                    ['error' => true]
                );
            }
        }
        return $this->returnResult('cam/*/', [], ['error' => true]);
    }

    /**
     * Provides an initialized Result object.
     *
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();

            $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response['params'] = $params;
            return $this->createJsonResult($response);
        }
        return $this->createRedirectResult()->setPath($path, $params);
    }

    /**
     * Define whether request is Ajax
     *
     * @return boolean
     */
    private function isAjax()
    {
        return $this->getRequest()->getParam('isAjax');
    }
}
