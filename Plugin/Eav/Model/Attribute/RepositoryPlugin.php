<?php

/**
 * Created on Sat Jan 28 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\Attribute;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Tangkoko\CustomerAttributesManagement\Api\CamAttributeRepositoryInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;
use Tangkoko\CustomerAttributesManagement\Model\Data\Condition\Converter;

class RepositoryPlugin
{
    /**
     *
     * @var CamAttributeRepositoryInterface
     */
    private $camAttributeRepository;

    /**
     *
     * @var CamAttributeFactory
     */
    private $camAttributeFactory;

    /**
     *
     * @var Converter
     */
    private $converter;

    /**
     *
     * @var Json
     */
    private $json;

    public function __construct(
        CamAttributeRepositoryInterface $camAttributeRepository,
        CamAttributeFactory $camAttributeFactory,
        Converter $converter,
        Json $json
    ) {
        $this->camAttributeRepository = $camAttributeRepository;
        $this->camAttributeFactory = $camAttributeFactory;
        $this->json = $json;
        $this->converter = $converter;
    }

    /**
     * Save cam attribute
     *
     * @param AttributeRepositoryInterface $subject
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @param string $result
     * @return string
     */
    public function afterSave(AttributeRepositoryInterface $subject, \Magento\Eav\Api\Data\AttributeInterface $attribute, $result)
    {

        $camAttribute = $attribute->getExtensionAttributes()->getCamAttribute();
        if (!$camAttribute) {
            $camAttribute = $this->camAttributeFactory->create();
        }

        if ($attribute->getData("visibility_conditions_arr")) {
            $camAttribute->loadPost($attribute->getData());

            $camAttribute->setAttributeId($attribute->getAttributeId())->setVisibilityConditionsSerialized($this->json->serialize($this->converter->dataModelToArray($camAttribute->getVisibilityConditions())));
        } else {
            $camAttribute->setAttributeId($attribute->getAttributeId())->setVisibilityConditionsSerialized($this->json->serialize([]));
        }

        $this->camAttributeRepository->save($camAttribute);
        return $result;
    }



    public function afterGet(AttributeRepositoryInterface $subject, \Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $camAttribute = $this->camAttributeRepository->get($attribute->getAttributeId());
        $camAttribute->setAttribute($attribute);
        $attribute->getExtensionAttributes()->setCamAttribute($camAttribute);
        return $attribute;
    }
}
