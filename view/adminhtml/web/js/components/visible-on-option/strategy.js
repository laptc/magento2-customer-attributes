/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(function () {
    'use strict';

    return {
        defaults: {
            valuesForOptions: [],
            imports: {
                toggleVisibility:
                    'cam_customer_attributes_form.cam_customer_attributes_form.base_fieldset.frontend_input:value'
            },
            isShown: false,
            inverseVisibility: false
        },

        /**
         * Toggle visibility state.
         *
         * @param {Number} selected
         */
        toggleVisibility: function (selected) {
            this.isShown = selected in this.valuesForOptions;
            this.visible(this.inverseVisibility ? !this.isShown : this.isShown);
        }
    };
});
