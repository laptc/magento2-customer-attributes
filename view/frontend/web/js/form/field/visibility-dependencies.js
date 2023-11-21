define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('cam.visibilityfielddependency', {
        options: {
            config: {},
            model: {}
        },

        _create: function() {
            const self = this;
            this.model = this.options.config.model;
            if(this.options.config.hasOwnProperty('visibility_conditions')){
                this.conditions = this.options.config.visibility_conditions;
                $.each(this.conditions, function(field, condition){
                    self.parseCondition(field, condition, self);
                    self.updateField(field);
                });
                
            }
        },
        parseCondition: function(field, condition){
            const self= this;
            switch(condition.type){
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Combine":
                    $.each(condition.conditions, function(index, subCondition){
                        self.parseCondition(field, subCondition);
                    });
                break;
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Address":
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Customer":
                    $("input[name="+ condition.attribute +"],select[name="+ condition.attribute +"]").change(function(){
                        self.updateField(field);
                    });
                break;
            }
        },
        updateField: function(field){
            if(this.isVisible(field)){
                $("input[name="+ field +"],select[name="+ field +"]").closest("div.field").show();
            }else{
                $("input[name="+ field+"],select[name="+ field +"]").closest("div.field").hide();
            }
        },
        isVisible: function(field){
            return this.validateCondition(this.conditions[field]);
        },
        validateCondition: function(condition){
            const self = this;
            switch(condition.type){
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Combine":
                    let result  =true;
                    $.each(condition.conditions, function(index, subCondition){
                        if(Boolean(condition.value) !==  Boolean(self.validateCondition(subCondition))){
                            result = false;
                        }
                    });

                    return result;
                break;
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Store":
                    switch(condition.operator){
                        case "()":
                        case "{}":
                            return condition.value.includes( this.options.config.storeId );
                        break;
                        case "!()":
                        case "!{}":
                            return !condition.value.includes( this.options.config.storeId );
                        break;
                    }
                    break;
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Address":
                case "Tangkoko\\CustomerAttributesManagement\\Model\\Rule\\Condition\\Customer":
                    const value = $("input[name="+ condition.attribute +"]:checked,select[name="+ condition.attribute +"]").val();
                    switch(condition.operator){
                        case "()":
                        case "{}":
                            return condition.value.includes(value);
                        break;
                        case "!()":
                        case "!{}":
                            return !condition.value.includes(value);
                        break;
                    }
                break;
            }
        }
    });

    return $.cam.visibilityfielddependency;
});
