<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

declare(strict_types=1);

namespace Tangkoko\CustomerAttributesManagement\Model\CamAttribute;

use Tangkoko\CustomerAttributesManagement\Api\CamAttributeRepositoryInterface;
use Tangkoko\CustomerAttributesManagement\Api\Data\CamAttributeInterface;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttributeFactory;
use Tangkoko\CustomerAttributesManagement\Model\ResourceModel\CamAttribute;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Tangkoko\CustomerAttributesManagement\Model\Data\CamAttribute as DataCamAttribute;

class Repository implements CamAttributeRepositoryInterface
{

    /**
     * @var CamAttribute
     */
    private $camAttributeResource;

    /**
     * @var CamAttributeFactory
     */
    private $camAttributeFactory;

    /**
     *
     * @param CamAttributeFactory $camAttributeFactory
     * @param CamAttribute $camAttributeResource
     */
    public function __construct(
        CamAttributeFactory $camAttributeFactory,
        CamAttribute $camAttributeResource
    ) {
        $this->camAttributeFactory = $camAttributeFactory;
        $this->camAttributeResource = $camAttributeResource;
    }

    public function save(CamAttributeInterface $camAttribute): CamAttributeInterface
    {
        try {
            $this->camAttributeResource->save($camAttribute);
        } catch (\Exception $e) {
            if ($camAttribute->getAttributeId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save cam attribute with ID %1. Error: %2',
                        [$camAttribute->getAttributeId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new cam attribute. Error: %1', $e->getMessage()));
        }

        return $camAttribute;
    }

    public function get(int $camAttributeId): CamAttributeInterface
    {
        /** @var DataCamAttribute $camAttribute */
        $camAttribute = $this->camAttributeFactory->create();
        $this->camAttributeResource->load($camAttribute, $camAttributeId);
        return $camAttribute;
    }


    public function delete(CamAttributeInterface $camAttribute): bool
    {
        try {
            $this->camAttributeResource->delete($camAttribute);
        } catch (\Exception $e) {
            if ($camAttribute->getAttributeId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove cam attribute with ID %1. Error: %2',
                        [$camAttribute->getAttributeId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove cam attribute. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById(int $camAttributeId): bool
    {
        $camAttribute = $this->getById((int)$camAttributeId);

        return $this->delete($camAttribute);
    }
}
