<?php

/**
 * Created on Sat Jan 28 2023
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2023 Tangkoko
 **/

namespace Tangkoko\CustomerAttributesManagement\Api;

use Tangkoko\CustomerAttributesManagement\Api\Data\CamAttributeInterface;

interface CamAttributeRepositoryInterface
{
    /**
     * save cam attribute
     *
     * @param CamAttributeInterface $camAttribute
     * @return CamAttributeInterface
     */
    public function save(CamAttributeInterface $camAttribute);

    /**
     * Return cam attribute
     *
     * @param integer $camAttributeId
     * @return CamAttributeInterface
     */
    public function get(int $camAttributeId);

    /**
     * delete cam attribute
     *
     * @param CamAttributeInterface $camAttribute
     * @return boolean
     */
    public function delete(CamAttributeInterface $camAttribute);

    /**
     * Delete cam attribute by id
     *
     * @param integer $camAttributeId
     * @return boolean
     */
    public function deleteById(int $camAttributeId);
}
