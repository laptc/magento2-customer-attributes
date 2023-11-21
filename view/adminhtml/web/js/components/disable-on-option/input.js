/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'Tangkoko_CustomerAttributesManagement/js/components/disable-on-option/strategy'
], function (Element, strategy) {
    'use strict';

    return Element.extend(strategy);
});
