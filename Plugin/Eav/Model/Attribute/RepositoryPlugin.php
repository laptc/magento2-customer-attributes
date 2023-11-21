<?php

/**
 * Created on Sat Jan 28 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\Attribute;

use Magento\Eav\Api\AttributeRepositoryInterface;

use Tangkoko\CustomerAttributesManagement\Api\CamAttributeRepositoryInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;


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
        CamAttributeFactory $camAttributeFactory
    ) {
        $this->camAttributeRepository = $camAttributeRepository;
        $this->camAttributeFactory = $camAttributeFactory;
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

        $camAttribute->setAttributeId($attribute->getAttributeId());

        $this->camAttributeRepository->save($camAttribute);
        return $result;
    }

    /**
     * Retrieve all attributes for entity type
     *
     * @param string $entityTypeCode
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeSearchResultsInterface
     */
    public function afterGetList(AttributeRepositoryInterface $subject, \Magento\Eav\Api\Data\AttributeSearchResultsInterface $list, $entityTypeCode, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        foreach ($list->getItems() as $item) {
            /** @var  \Magento\Eav\Api\Data\AttributeInterface $item */
            $item->getExtensionAttributes()->getCamAttribute()->setAttribute($item);
        }
        return $list;
    }



    public function afterGet(AttributeRepositoryInterface $subject, \Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $camAttribute = $this->camAttributeRepository->get($attribute->getAttributeId());
        $camAttribute->setAttribute($attribute);
        $attribute->getExtensionAttributes()->setCamAttribute($camAttribute);
        return $attribute;
    }
}
