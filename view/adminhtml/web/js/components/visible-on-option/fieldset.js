/**
 * Copyright © Mvn, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/components/fieldset',
    'Mvn_Cam/js/components/visible-on-option/strategy'
], function (Fieldset, strategy) {
    'use strict';

    return Fieldset.extend(strategy).extend(
        {
            defaults: {
                openOnShow: true
            },

            /**
             * Toggle visibility state.
             */
            toggleVisibility: function () {
                this._super();

                if (this.openOnShow) {
                    this.opened(this.inverseVisibility ? !this.isShown : this.isShown);
                }
            }
        }
    );
});
