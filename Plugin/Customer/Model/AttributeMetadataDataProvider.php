<?php

/**
 * Created on Wed Mar 08 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Customer\Model;

use Magento\Customer\Model\AttributeMetadataDataProvider as Subject;
use Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute\CollectionFactory;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;

class AttributeMetadataDataProvider
{

    /**
     *
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     *
     * @var CamAttributeFactory
     */
    private CamAttributeFactory $camAttributeFactory;


    public function __construct(
        CollectionFactory $collectionFactory,
        CamAttributeFactory $camAttributeFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->camAttributeFactory = $camAttributeFactory;
    }


    public function afterLoadAttributesCollection(Subject $subject, \Magento\Customer\Model\ResourceModel\Form\Attribute\Collection $result)
    {
        $ids = $result->getAllIds();
        $collection = $this->collectionFactory->create()->addFieldToFilter("attribute_id", ["in" => $ids]);
        foreach ($result as $item) {

            $camAttribute = $collection->getItemById($item->getAttributeId()) ??  $this->camAttributeFactory->create();

            /**
             * @var \Magento\EAv\Model\Attribute $item
             */

            $camAttribute->setAttribute($item);
            $item->getExtensionAttributes()->setCamAttribute($camAttribute);
        }
        return $result;
    }
}
