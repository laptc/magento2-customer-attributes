/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Catalog/js/form/element/checkbox',
    'uiRegistry'
], function (Checkbox, registry) {
    'use strict';

    return Checkbox.extend({

        /**
         * Configure data scope.
         */
        configureDataScope: function () {
            var recordId,
                value,
                isInitialize;

            var parent = registry.get("name = "+this.parentName);
            isInitialize = (parent && parent.get("data").initialize)?true:false;

            // Get recordId
            recordId = (parent && parent.recordId)?parent.recordId:this.parentName.split('.').last();

            value = this.prefixElementName + recordId;

            this.dataScope = 'data.' + this.inputCheckBoxName;
            this.inputName = this.dataScopeToHtmlArray(this.inputCheckBoxName);

            this.initialValue = value;

            this.links.value = this.provider + ':' + this.dataScope;
        }
    });
});
