/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(function () {
    'use strict';

    return {
        defaults: {
            valuesForEnable: [],
            disabled: true,
            imports: {
                toggleDisable:
                    'cam_customer_attributes_form.cam_customer_attributes_form.base_fieldset.frontend_input:value'
            }
        },

        /**
         * Toggle disabled state.
         *
         * @param {Number} selected
         */
        toggleDisable: function (selected) {
            this.disabled(!(selected in this.valuesForEnable));
        }
    };
});
