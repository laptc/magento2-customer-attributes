/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'uiRegistry'
], function (Checkbox, registry) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            inputCheckBoxName: '',
            prefixElementName: '',
            parentDynamicRowName: 'visual_swatch'
        },

        /**
         * Parses options and merges the result with instance
         *
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();
            this.configureDataScope();

            return this;
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if (this.initialChecked) {
                this.checked(true);
            }

            return this;
        },

        /**
         * Configure data scope.
         */
        configureDataScope: function () {
            var recordId,
                value,
                isInitialize,
                isDefault;

            var parent = registry.get("name = "+this.parentName);
            isInitialize = (parent && parent.get("data").initialize)?true:false;
            isDefault = (parent && parent.get("data").is_default)?true:false;


            // Get recordId
            recordId = (parent && parent.recordId)?parent.recordId:this.parentName.split('.').last();
            this.recordId = recordId;

            value = isInitialize?recordId:this.prefixElementName + recordId;

            this.dataScope = 'data.' + this.inputCheckBoxName;
            this.inputName = this.dataScopeToHtmlArray(this.inputCheckBoxName);

            this.initialValue = value;
            this.initialChecked = isDefault;

            this.links.value = this.provider + ':' + this.dataScope;

        },

        /**
         * Get HTML array from data scope.
         *
         * @param {String} dataScopeString
         * @returns {String}
         */
        dataScopeToHtmlArray: function (dataScopeString) {
            var dataScopeArray, dataScope, reduceFunction;

            /**
             * Add new level of nesting.
             *
             * @param {String} prev
             * @param {String} curr
             * @returns {String}
             */
            reduceFunction = function (prev, curr) {
                return prev + '[' + curr + ']';
            };

            dataScopeArray = dataScopeString.split('.');

            dataScope = dataScopeArray.shift();
            dataScope += dataScopeArray.reduce(reduceFunction, '');

            return dataScope;
        }
    });
});
