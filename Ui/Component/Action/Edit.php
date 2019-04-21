<?php
/**
 * Copyright © 2019 Mvn. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mvn\Cam\Ui\Component\Action;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Edit extends Column
{
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_urlPath;

    /**
     * @var string
     */
    protected $_indexField;

    /**
     * @var string
     */
    protected $_labelAction;

    /**
     * View constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_urlPath = $data['config']['urlPath'];
        $this->_indexField = $data['config']['indexField'];
        $this->_labelAction = $data['config']['labelAction'];
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item[$this->_indexField])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl($this->_urlPath, [$this->_indexField => $item[$this->_indexField]]),
                        'label' => __($this->_labelAction)
                    ];
                }
            }
        }
        return $dataSource;
    }
}