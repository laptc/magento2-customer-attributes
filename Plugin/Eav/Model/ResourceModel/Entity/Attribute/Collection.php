<?php

/**
 * Created on Fri Feb 17 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Plugin\Eav\Model\ResourceModel\Entity\Attribute;

use Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute\CollectionFactory;
//use Magento\Customer\Model\ResourceModel\Attribute\Collection;
//use Magento\Customer\Model\ResourceModel\Attribute\Collection;

class Collection
{

    /**
     *
     * @var CollectionFactory
     */
    private $collectionFactory;


    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function afterGetEntityAttributes(\Magento\Eav\Model\Config $subject, $result)
    {

        $ids = array_map(function ($attribute) {
            return $attribute->getAttributeId();
        }, $result);
        /**
         * @var \Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute\Collection $collection
         */
        $collection = $this->collectionFactory->create()->addFieldToFilter("attribute_id", ["in" => $ids]);

        foreach ($result as $item) {
            /**
             * @var \Magento\EAv\Model\Attribute $item
             */
            if ($camAttribute = $collection->getItemById($item->getAttributeId())) {
                $camAttribute->setAttribute($item);
                $item->getExtensionAttributes()->setCamAttribute($camAttribute);
            }
        }

        return $result;
    }
}
