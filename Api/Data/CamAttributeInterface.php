<?php

/**
 * Created on Fri Jan 27 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Api\Data;

interface CamAttributeInterface
{

    const ATTRIBUTE_ID = "attribute_id";

    const VISIBILITY_CONDITIONS = "visibility_conditions";

    const VISIBILITY_CONDITIONS_SERIALIZED = "visibility_conditions_serialized";

    const FIELDSET = "fieldset";

    const PLACEHOLDER = "placeholder";

    /**
     * Return int
     *
     * @return int
     */
    public function getAttributeId();

    /**
     * set AttributeId
     * @param int $attributeId
     * @return self
     */
    public function setAttributeId($attributeId): self;

    /**
     * @return \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface[]|null
     * 
     */
    public function getVisibilityConditions();

    /**
     * set condition
     * @param \Tangkoko\CustomerAttributesManagement\Api\Data\ConditionInterface $conditions
     * @return self
     */
    public function setVisibilityConditions($conditions);

    /**
     * return json conditoins serialized
     * 
     * @return string
     */
    public function getVisibilityConditionsSerialized();


    /**
     * set condition
     * @param string $conditions
     * @return self
     */
    public function setVisibilityConditionsSerialized($conditions);


    /**
     * get fieldset
     * @param string $fieldset
     * @return self
     */
    public function getFieldset();
    /**
     * set fieldset
     * @param string $fieldset
     * @return self
     */
    public function setFieldset(string $fieldset);

    /**
     * get placeholder
     * @param string $fieldset
     * @return self
     */
    public function getPlaceholder();

    /**
     * set placeholder
     * @param string $placeholder
     * @return self
     */
    public function setPlaceholder(string $placeholder);
}
