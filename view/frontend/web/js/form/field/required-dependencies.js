define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('cam.requiredfielddependency', {
        options: {
            config: {},
            model: {}
        },

        _create: function() {
            const self = this;
            this.model = this.options.config.model;
            if(this.options.config.hasOwnProperty('required_conditions')){
                this.conditions = this.options.config.required_conditions;
                $.each(this.conditions, function(field, condition){
                    self.parseCondition(field, condition, self);
                    self.updateField(field);
                    self.element.validate().resetForm();
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
                        self.element.validate().resetForm();
                    });
                break;
            }
        },
        updateField: function(field){
            let fieldElement = $("input[name="+ field +"],select[name="+ field +"]");
            if(!fieldElement.attr("data-initvalidate")){
                fieldElement.attr("data-initvalidate", fieldElement.attr("data-validate"));
            }
            let isRequired = this.isRequired(field);
            if(isRequired === true){
                $("div.field."+field).addClass("required");
                fieldElement.attr("data-validate", fieldElement.attr("data-initvalidate"));
            }else if(isRequired === false){
                $("div.field."+field).removeClass("required");
                fieldElement.attr("data-validate", "{}");
            }
            
        },
        isRequired: function(field){
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
                    let value = null;
                    if($("input[name="+ condition.attribute +"]:checked,select[name="+ condition.attribute +"]").length > 0){
                        value = $("input[name="+ condition.attribute +"]:checked,select[name="+ condition.attribute +"]").val();
                    }else{
                        value = self.options.config.model[condition.attribute];
                    }
                    if(value === null){
                        return value;
                    }
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

    return $.cam.requiredfielddependency;
});
