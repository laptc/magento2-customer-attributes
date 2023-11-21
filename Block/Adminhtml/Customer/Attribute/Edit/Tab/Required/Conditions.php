<?php

/**
 * Created on Fri Jan 27 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Block\Adminhtml\Customer\Attribute\Edit\Tab\Required;


use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use \Magento\Customer\Model\Attribute;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttribute;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;
use Tangkoko\CustomerAttributesManagement\Block\Adminhtml\Customer\Attribute\Edit\Tab\RequiredConditions;

/**
 * Block for rendering Conditions tab on Sales Rules creation page.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var RequiredConditions
     */
    protected $_conditions;

    /**
     * @var string
     */
    protected $_nameInLayout = 'required_conditions';


    /**
     *
     * @var CamAttributeFactory
     */
    protected CamAttributeFactory $camAttributeFactory;

    /**
     *
     * @var \Magento\Eav\Model\Config
     */
    protected  \Magento\Eav\Model\Config $eavConfig;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param CamAttributeFactory $camAttributeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        RequiredConditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        CamAttributeFactory $camAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->camAttributeFactory = $camAttributeFactory;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Required Conditions');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Required Conditions');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * @var Attribute $model
         */
        $model = $this->_coreRegistry->registry("entity_attribute");
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of conditions tab to supplied form.
     *
     * @param Attribute $attribute
     * @param string $fieldsetId
     * @param string $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($attribute, $fieldsetId = 'requiredconditions_fieldset', $formName = 'cam_customer_attributes_form')
    {

        $model = $attribute->getExtensionAttributes()->getCamAttribute();
        if (is_null($model)) {
            $model = $this->camAttributeFactory->create()->setAttribute($attribute);
        }
        $attribute->getExtensionAttributes()->setCamAttribute($model);
        $attribute->setEntityTypeId($this->eavConfig->getEntityType(\Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER)->getId());

        $conditionsFieldSetId = $formName . $fieldsetId . '_' . $attribute->getId();
        $newChildUrl = $this->getUrl(
            'cam/customer/newRequiredConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('test');

        $renderer = $this->getLayout()->createBlock(Fieldset::class);
        $renderer->setTemplate(
            'Tangkoko_CustomerAttributesManagement::customer/required/conditions/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Attribute is mandatory on forms only if conditions are met.(leave blank to always display attributes with visibility field to true)'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'required_conditions',
            'text',
            [
                'name'           => 'required_conditions',
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        //$model->setForm($form);
        $form->setValues($attribute->getData());
        $this->setConditionFormName($model->getRequiredConditions(), $formName);
        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getRequiredConditions() && is_array($conditions->getRequiredConditions())) {
            foreach ($conditions->getRequiredConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
