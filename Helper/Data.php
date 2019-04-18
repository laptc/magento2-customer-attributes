<?php

/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Helper;

/**
 * Class Data
 * @package Mvn\Cam\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $configResource;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    protected $reinitableConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $configResource
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $configResource,
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig
    ) {
        parent::__construct($context);
        $this->configResource = $configResource;
        $this->reinitableConfig = $reinitableConfig;
    }

    /**
     * @param $path
     * @param string $scope
     * @return mixed
     */
    public function getStoreConfig($path, $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT){
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     * @param $path
     * @param $value
     * @param string $scope
     * @return $this
     */
    public function saveConfig($path, $value, $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT){
        $this->configResource->saveConfig(
            $path,
            $value,
            $scope,
            0
        );
        return $this;
    }

    /**
     * @return $this
     */
    public function reinitConfig(){
        $this->reinitableConfig->reinit();
        return $this;
    }
}