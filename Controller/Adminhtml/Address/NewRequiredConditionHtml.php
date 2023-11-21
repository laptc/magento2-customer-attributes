<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tangkoko\CustomerAttributesManagement\Controller\Adminhtml\Address;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\ConditionInterface;
use Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

/**
 * Controller class NewConditionHtml. Returns condition html
 */
class NewRequiredConditionHtml extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Tangkoko_CustomerAttributesManagement::address_attributes';



    /**
     * New condition html action
     *
     * @return void
     */
    public function execute()
    {
        $formName = $this->getRequest()->getParam('form_namespace');
        $id = $this->getRequest()->getParam('id');

        $typeArray = explode(
            '|',
            str_replace('-', '/', $this->getRequest()->getParam('type', ''))
        );
        $type = $typeArray[0];

        if ($type && class_exists($type) && !in_array(ConditionInterface::class, class_implements($type))) {
            $html = '';
            $this->getResponse()->setBody($html);
            return;
        }

        $model = $this->_objectManager->create(
            $type
        )->setId(
            $id
        )->setType(
            $type
        )->setRule(
            $this->_objectManager->create(\Tangkoko\CustomerAttributesManagement\Model\Data\CamAttribute::class)
        )->setPrefix(
            'required_conditions'
        );
        if (!empty($typeArray[1])) {
            $model->setAttribute($typeArray[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);

            $this->setJsFormObject($model);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Set jsFormObject for the model object
     *
     * @return void
     * @param AbstractCondition $model
     */
    private function setJsFormObject(AbstractCondition $model): void
    {
        $requestJsFormName = $this->getRequest()->getParam('form');
        $actualJsFormName = $this->getJsFormObjectName($model->getFormName());
        if ($requestJsFormName === $actualJsFormName) { //new
            $model->setJsFormObject($actualJsFormName);
        }
    }

    /**
     * Get jsFormObject name
     *
     * @param string $formName
     * @return string
     */
    private function getJsFormObjectName(string $formName): string
    {
        return $formName . 'rule_required_conditions_fieldset_';
    }
}
