/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/select',
    'Mvn_Cam/js/components/visible-on-option/strategy',
    'Mvn_Cam/js/components/disable-on-option/strategy'
], function (Element, visibleStrategy, disableStrategy) {
    'use strict';

    return Element.extend(visibleStrategy).extend(disableStrategy);
});
