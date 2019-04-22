/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
], function (DynamicRows) {
    'use strict';

    return DynamicRows.extend({
        /**
         * Initialize children
         *
         * @returns {Object} Chainable.
         */
        initChildren: function () {
            this.showSpinner(true);
            this.getChildItems().forEach(function (data, index) {
                var itemId = this.startIndex + index;
                if(this.identificationProperty && (typeof  data[this.identificationProperty] != "undefined")){
                    itemId = data[this.identificationProperty];
                }
                this.addChild(data, this.startIndex + index, itemId);
            }, this);

            return this;
        },
    });
});
