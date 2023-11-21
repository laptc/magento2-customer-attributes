<?php

/**
 * Created on Fri Jan 27 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/


namespace Tangkoko\CustomerAttributesManagement\Model\Data;

use Tangkoko\CustomerAttributesManagement\Model\Attribute\DefaultFieldsetResolver;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Model\AbstractModel;
use Tangkoko\CustomerAttributesManagement\Api\Data\CamAttributeInterface;
use Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute as ResourceModelCamAttribute;

class CamAttribute extends AbstractModel implements CamAttributeInterface
{

    /**
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     *
     * @var \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\CombineFactory
     */
    protected $condCombineFactory;

    /**
     *
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $form;


    /**
     *
     * @var \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Address\CombineFactory
     */
    protected $addressCondCombineFactory;

    /**
     *
     * @var DefaultFieldsetResolver
     */
    protected  DefaultFieldsetResolver $defaultFieldsetResolver;

    /**
     *
     * @var \Magento\Eav\Model\Config
     */
    protected  \Magento\Eav\Model\Config $eavConfig;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\Data\FormFactory $formFactory,
        DefaultFieldsetResolver $defaultFieldsetResolver,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eavConfig = $eavConfig;
        $this->serializer = $serializer;
        $this->condCombineFactory = $condCombineFactory;
        $this->formFactory = $formFactory;
        $this->defaultFieldsetResolver = $defaultFieldsetResolver;
    }


    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct() //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
        $this->_init(ResourceModelCamAttribute::class);
    }

    /**
     * Return int
     *
     * @return int
     */
    public function getAttributeId()
    {
        return $this->getData(static::ATTRIBUTE_ID);
    }

    /**
     * set AttributeId
     * @param int $attributeId
     * @return self
     */
    public function setAttributeId($attributeId): self
    {
        $this->setData(static::ATTRIBUTE_ID, $attributeId);
        return $this;
    }

    /**
     * set AttributeId
     * @param AttributeInterface $attribute
     * @return self
     */
    public function setAttribute($attribute): self
    {
        $this->setData("attribute", $attribute);
        $this->setAttributeId($attribute->getAttributeId());
        return $this;
    }

    /**
     * @return \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface|null
     * 
     */
    public function getConditions()
    {

        return $this->getVisibilityConditions();
    }

    /**
     * @return \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface|null
     * 
     */
    public function getVisibilityConditions()
    {

        if (empty($this->getData(static::VISIBILITY_CONDITIONS))) {
            $this->_resetConditions();

            $conditionsInstance = $this->getData(static::VISIBILITY_CONDITIONS);
            // Load rule conditions if it is applicable
            if ($this->hasVisibilityConditionsSerialized()) {
                /**
                 * @var \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine $conditionsInstance
                 */
                $conditions = $this->getVisibilityConditionsSerialized();
                if (!empty($conditions)) {

                    $conditions = $this->serializer->unserialize($conditions);
                    if (is_array($conditions) && !empty($conditions)) {
                        $conditionsInstance->loadArray($conditions);
                    }
                }
            }
        }

        return $this->getData(static::VISIBILITY_CONDITIONS);
    }

    /**
     * Reset rule combine conditions
     *
     * @param null|\Magento\Rule\Model\Condition\Combine $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null)
    {

        if (null === $conditions) {
            $conditions = $this->getConditionsInstance(['prefix' => 'conditions']);
        }
        $conditions->setRule($this)->setId('1');
        $this->setVisibilityConditions($conditions);

        return $this;
    }


    /**
     * Reset rule combine conditions
     *
     * @param null|\Magento\Rule\Model\Condition\Combine $conditions
     * @return $this
     */
    protected function _resetRequiredConditions($conditions = null)
    {

        if (null === $conditions) {
            $conditions = $this->getConditionsInstance(['prefix' => 'required_conditions']);
        }
        $conditions->setRule($this)->setId('1');
        $this->setRequiredConditions($conditions);
        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance(?array $data = [])
    {
        /**
         * @var AttributeInterface $attribute
         */
        $attribute = $this->getAttribute();
        return $this->condCombineFactory->create($this->eavConfig->getEntityType($attribute->getEntityTypeId())->getEntityTypeCode(), $data);
    }

    /**
     * set condition
     * @param \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface[] $conditions
     * @return self
     */
    public function setVisibilityConditions($conditions)
    {
        $this->setData(static::VISIBILITY_CONDITIONS, $conditions);
        return $this;
    }

    /**
     * return json conditoins serialized
     * 
     * @return string
     */
    public function getVisibilityConditionsSerialized()
    {

        return $this->getData(static::VISIBILITY_CONDITIONS_SERIALIZED);
    }

    /**
     * Rule form getter
     *
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        if (!$this->form) {
            $this->form = $this->formFactory->create();
        }
        return $this->form;
    }

    /**
     * set condition
     * @param string $conditions
     * @return self
     */
    public function setVisibilityConditionsSerialized($conditions)
    {
        $this->setData(static::VISIBILITY_CONDITIONS_SERIALIZED, $conditions);
        return $this;
    }


    /**
     * @return \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface[]|null
     * 
     */
    public function getRequiredConditions()
    {

        if (empty($this->getData(static::REQUIRED_CONDITIONS))) {

            $this->_resetRequiredConditions();
            $conditionsInstance = $this->getData(static::REQUIRED_CONDITIONS);
            // Load rule conditions if it is applicable
            if ($this->hasRequiredConditionsSerialized()) {
                /**
                 * @var \Tangkoko\CustomerAttributesManagement\Model\Rule\Condition\Combine $conditionsInstance
                 */
                $conditions = $this->getRequiredConditionsSerialized();
                if (!empty($conditions)) {

                    $conditions = $this->serializer->unserialize($conditions);
                    if (is_array($conditions) && !empty($conditions)) {
                        $conditionsInstance->loadArray($conditions, 'conditions');
                    }
                }
            }
        }
        return $this->getData(static::REQUIRED_CONDITIONS);
    }

    /**
     * set condition
     * @param \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface $conditions
     * @return self
     */
    public function setRequiredConditions($conditions)
    {
        $this->setData(static::REQUIRED_CONDITIONS, $conditions);
        return $this;
    }

    /**
     * return json conditoins serialized
     * 
     * @return string
     */
    public function getRequiredConditionsSerialized()
    {
        return $this->getData(static::REQUIRED_CONDITIONS_SERIALIZED);
    }


    /**
     * set condition
     * @param string $conditions
     * @return self
     */
    public function setRequiredConditionsSerialized($conditions)
    {
        $this->setData(static::REQUIRED_CONDITIONS_SERIALIZED, $conditions);
        return $this;
    }

    /**
     * get fieldset
     * @param string $fieldset
     * @return self
     */
    public function getFieldset()
    {
        return $this->getData(static::FIELDSET) ??  $this->defaultFieldsetResolver->getDefaultFieldset($this->getAttribute());
    }

    /**
     * set fieldset
     * @param string $fieldset
     * @return self
     */
    public function setFieldset(string $fieldset)
    {
        $this->setData(static::FIELDSET, $fieldset);
        return $this;
    }

    /**
     * get placeholder
     * @param string $fieldset
     * @return self
     */
    public function getPlaceholder()
    {
        return $this->getData(static::PLACEHOLDER);
    }

    /**
     * set placeholder
     * @param string $placeholder
     * @return self
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->setData(static::PLACEHOLDER, $placeholder);
        return $this;
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        $this->_resetConditions();
        $this->_resetRequiredConditions();


        $arr = $this->_convertFlatToRecursive($data['rule']);


        if (isset($arr['conditions'])) {
            $this->getConditions()->setVisibilityConditions([])->loadArray($arr['conditions'][1]);
        }

        if (isset($arr['required_conditions'])) {

            $this->getRequiredConditions()->setRequiredConditions([])->loadArray($arr['required_conditions'][1], 'required_conditions');
        }

        $this->setFieldset($data["fieldset"]);
        $this->setPlaceholder($data["placeholder"]);
        return $this;
    }

    /**
     * Set specified data to current rule.
     * Set conditions and actions recursively.
     * Convert dates into \DateTime.
     *
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _convertFlatToRecursive(array $data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'required_conditions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = &$arr;
                    for ($i = 0, $l = count($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * Return true if attribute is visible
     *
     * @return boolean
     */
    public function isVisible(AbstractModel $model)
    {
        return $this->getConditions()->isVisible($model);
    }

    /**
     * Return true if attribute is visible
     *
     * @return boolean
     */
    public function isRequired(AbstractModel $model)
    {
        return $this->getRequiredConditions()->validate($model);
    }

    /**
     * Return true if attribute is visible
     *
     * @return boolean
     */
    public function validate(AbstractModel $model)
    {
        return $this->getConditions()->validate($model);
    }
}
