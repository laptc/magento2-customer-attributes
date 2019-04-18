/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'Mvn_Cam/js/components/visible-on-option/strategy'
], function (Element, strategy) {
    'use strict';

    return Element.extend(strategy);
});
