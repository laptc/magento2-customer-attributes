/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Catalog/js/form/element/action-delete',
    'uiRegistry'
], function (ActionDelete, registry) {
    'use strict';

    return ActionDelete.extend({
        /**
         * Configure data scope.
         */
        configureDataScope: function () {
            var recordId,
                prefixName,
                suffixName,
                isInitialize;

            var parent = registry.get("name = "+this.parentName);
            isInitialize = (parent && parent.get("data").initialize)?true:false;

            // Get recordId
            recordId = (parent && parent.recordId)?parent.recordId:this.parentName.split('.').last();

            prefixName = this.dataScopeToHtmlArray(this.prefixName);
            this.elementName = (isInitialize)?recordId:this.prefixElementName + recordId;

            suffixName = '';

            if (!_.isEmpty(this.suffixName) || _.isNumber(this.suffixName)) {
                suffixName = '[' + this.suffixName + ']';
            }
            this.inputName = prefixName + '[' + this.elementName + ']' + suffixName;

            suffixName = '';

            if (!_.isEmpty(this.suffixName) || _.isNumber(this.suffixName)) {
                suffixName = '.' + this.suffixName;
            }
            this.dataScope = 'data.' + this.prefixName + '.' + this.elementName + suffixName;

            this.links.value = this.provider + ':' + this.dataScope;
        },

        /**
         * Delete record instance
         * update data provider dataScope
         *
         * @param {Object} parents
         */
        deleteItem: function (parent, index, recordId) {
            this.value(1);
            parent.deleteRecord(index, recordId);

            return this;
        }
    });
});
