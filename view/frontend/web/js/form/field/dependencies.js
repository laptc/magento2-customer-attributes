define([
    'jquery',
    'jquery/ui'
], function ($, ui) {
    'use strict';

    $.widget('cam.fielddependency', {
        options: {
            config: {},
            model: {}
        },

        _create: function() {

            this.element.mage('camVisibilityDependencies', {
                config: this.options.config
            });
            this.element.mage('camRequiredDependencies', {
                config: this.options.config
            });
            
            
        }
    });

    return $.cam.fielddependency;
});
