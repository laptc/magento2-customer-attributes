<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Block\Attributes;

use Magento\Framework\Api\ArrayObjectSearch;

/**
 * Class Date
 * @package Mvn\Cam\Block\Attributes
 */
class Date extends AbstractElement
{

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "Mvn_Cam::attributes/date.phtml";

    /**
     * Constants for borders of date-type customer attributes
     */
    const MIN_DATE_RANGE_KEY = 'date_range_min';

    const MAX_DATE_RANGE_KEY = 'date_range_max';

    /**
     * Date inputs
     *
     * @var array
     */
    protected $_dateInputs = [];

    /**
     * @var \Magento\Framework\View\Element\Html\Date
     */
    protected $dateElement;

    /**
     * @var \Magento\Framework\Data\Form\FilterFactory
     */
    protected $filterFactory;

    /**
     * Date constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Element\Html\Date $dateElement
     * @param \Magento\Framework\Data\Form\FilterFactory $filterFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Element\Html\Date $dateElement,
        \Magento\Framework\Data\Form\FilterFactory $filterFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateElement = $dateElement;
        $this->filterFactory = $filterFactory;
    }

    /**
     * @return string
     */
    public function getAttributeValue(){
        $value = parent::getAttributeValue();
        if($value){
            $dateFormat = $this->getDateFormat();
            $value = $this->_localeDate->date($value)->format(str_replace("M", "m", $dateFormat));
        }
        return $value;
    }

    /**
     * Create correct date field
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $html = "";
        $attribute = $this->getAttribute();
        if($attribute){
            $this->dateElement->setData([
                'extra_params' => $this->getHtmlExtraParams(),
                'name' => $attribute->getAttributeCode(),
                'id' => $attribute->getAttributeCode(),
                'class' => $this->getHtmlClass(),
                'value' => $this->getAttributeValue(),
                'date_format' => $this->getDateFormat(),
                'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
                'change_month' => 'true',
                'change_year' => 'true',
                'show_on' => 'both',
                'first_day' => $this->getFirstDay()
            ]);
            $html = $this->dateElement->getHtml();
        }
        return $html;
    }

    /**
     * Return data-validate rules
     *
     * @return string
     */
    public function getHtmlExtraParams()
    {
        $validators = [];
        $attribute = $this->getAttribute();
        if ($attribute !== null) {
            if ($attribute->isRequired()) {
                $validators['required'] = true;
            }
            $validators['validate-date'] = [
                'dateFormat' => $this->getDateFormat()
            ];
        }
        return 'data-validate="' . $this->_escaper->escapeHtml(json_encode($validators)) . '"';
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_localeDate->getDateFormatWithLongYear();
    }

    /**
     * Add date input html
     *
     * @param string $code
     * @param string $html
     * @return void
     */
    public function setDateInput($code, $html)
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     *
     * @param bool $stripNonInputChars
     *
     * @return string
     */
    public function getSortedDateInputs($stripNonInputChars = true)
    {
        $mapping = [];
        if ($stripNonInputChars) {
            $mapping['/[^medy]/i'] = '\\1';
        }
        $mapping['/m{1,5}/i'] = '%1$s';
        $mapping['/e{1,5}/i'] = '%2$s';
        $mapping['/d{1,5}/i'] = '%2$s';
        $mapping['/y{1,5}/i'] = '%3$s';

        $dateFormat = preg_replace(array_keys($mapping), array_values($mapping), $this->getDateFormat());

        return sprintf($dateFormat, $this->_dateInputs['m'], $this->_dateInputs['d'], $this->_dateInputs['y']);
    }

    /**
     * Return minimal date range value
     *
     * @return string|null
     */
    public function getMinDateRange()
    {
        $attribute = $this->getAttribute();
        if ($attribute !== null) {
            $rules = $attribute->getValidationRules();
            $minDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MIN_DATE_RANGE_KEY
            );
            if ($minDateValue !== null) {
                return date("Y/m/d", $minDateValue);
            }
        }
        return null;
    }

    /**
     * Return maximal date range value
     *
     * @return string|null
     */
    public function getMaxDateRange()
    {
        $attribute = $this->getAttribute();
        if ($attribute !== null) {
            $rules = $attribute->getValidationRules();
            $maxDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MAX_DATE_RANGE_KEY
            );
            if ($maxDateValue !== null) {
                return date("Y/m/d", $maxDateValue);
            }
        }
        return null;
    }

    /**
     * Return first day of the week
     *
     * @return int
     */
    public function getFirstDay()
    {
        return (int)$this->_scopeConfig->getValue(
            'general/locale/firstday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
