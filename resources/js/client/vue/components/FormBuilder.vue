<template>
    <form :class="formInfo.form_class" v-if="form && true">
        <div v-if="formInfo.title">{{ formInfo.title }}</div>
        <div v-if="formInfo.description">{{ formInfo.description }}</div>
        <div v-for="(field, key) in form" :class="field.container_field_class ?? 'fields_group'" :key="key">
            <div v-if="key.includes('fields_')" :class="field.container_field_class" v-for="(field, subKey) in field"
                 :key="subKey">
                <component
                    :is="field.vue_field_component"
                    :id="field.id"
                    :name="subKey"
                    :label="field.label"
                    :labelClassName="field.label_class_name"
                    :value="field.value"
                    :modelValue="formModel[subKey]"
                    :type="field.type"
                    :placeholder="field.placeholder"
                    :class-name="field.field_class"
                    :errors="v$.formModel[subKey]"
                    :error-class="field.error_class"
                    :mask="field.mask ?? ''"
                    :multiple="field.multiple ?? false"
                    :accept="field.accept ?? ''"
                    @update:modelValue="updateField(subKey, $event);"
                />
            </div>
            <component
                v-else
                :is="field.vue_field_component"
                :name="key"
                :id="field.id"
                :label="field.label"
                :labelClassName="field.label_class_name"
                :value="field.value"
                :modelValue="formModel[key]"
                :type="field.type"
                :placeholder="field.placeholder"
                :class-name="field.field_class"
                :errors="v$.formModel[key]"
                :error-class="field.error_class"
                :mask="field.mask ?? ''"
                :multiple="field.multiple ?? false"
                :accept="field.accept ?? ''"
                @update:modelValue="updateField(key, $event);"
            />
        </div>
        <button @click.prevent="onSubmit" :class="formInfo.button_class">{{ formInfo.button_text }}</button>
    </form>
</template>

<script>
import axios from "axios";
import {isInteger, toNumber} from "lodash/lang";
import InputComponent from './fields/InputComponent.vue'
import SelectComponent from "./fields/SelectComponent.vue";
import DatePickerComponent from "./fields/DatePickerComponent.vue";
import FileComponent from "./fields/FileComponent.vue";
import TextAreaComponent from "./fields/TextAreaComponent.vue";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import {
    required,
    minLength,
    maxLength,
    helpers,
    email,
    numeric,
    alpha,
    alphaNum,
    integer,
    decimal,
    requiredIf,
    requiredUnless,
    between, sameAs
} from '@vuelidate/validators';

export default {
    name: 'FormBuilder',
    components: {
        InputComponent,
        TextAreaComponent,
        SelectComponent,
        DatePickerComponent,
        FileComponent
    },
    props: {
        name: String
    },
    data() {
        return {
            form: {},
            formInfo: {},
            formModel: {},
            v$: useVuelidate(),
            validations: {},
            formData: {},
        };
    },
    mounted() {
        this.getFormConfig();
    },
    methods: {
        toNumber,
        isInteger,
        onSubmit() {
            this.v$.$validate();
            if (this.v$.$error) {
                console.log('error');
            } else {
                console.log('success');
                this.fillFormData();
                console.log(this.formData)
                this.sendFormData();
            }
        },
        fillFormData() {
            this.formData = new FormData();
            console.log(this.formModel)
            for (let key in this.formModel) {
                if (Array.isArray(this.formModel[key])) {
                    for (let file of this.formModel[key]) {
                        this.formData.append(key + '[]', file);
                    }
                } else if (this.formModel[key] instanceof File) {
                    this.formData.append(key, this.formModel[key]);
                } else if (
                    typeof this.formModel[key] === 'object' &&
                    this.formModel[key].value !== null
                ) {
                    this.formData.append(key, this.formModel[key].value);
                } else {
                    this.formData.append(key, this.formModel[key]);
                }
            }
        },
        async sendFormData() {
            try {
                const response = await axios.post('/ajax/forms/' + this.name + '/send', this.formData);
                console.log('Form sent successfully', response);
            } catch (error) {
                console.error('Error sending form', error);
            }
        },
        updateField(fieldName, value) {
            this.formModel[fieldName] = value;
        },
        async getFormConfig() {
            try {
                const response = await axios.post('/ajax/get-form-config/' + this.name);
                const formConfig = response.data;
                this.form = formConfig.fields || null;
                this.formInfo = formConfig?.view || null;
                const {messages} = this.extractValidationRulesAndMessages(this.form);
                this.validations = messages;
                this.formModel = this.generateFormModel(this.form);
            } catch (error) {
                console.log(error);
            }
        },

        createRule(ruleName) {
            if (ruleName.includes('max')) {
                const ruleValue = parseInt(ruleName.split(':')[1]); // Получаем значение из строки
                return maxLength(ruleValue);
            } else if (ruleName.includes('min')) {
                const ruleValue = parseInt(ruleName.split(':')[1]); // Получаем значение из строки
                return minLength(ruleValue);
            } else if (ruleName === 'required') {
                return required;
            } else if (ruleName === 'required_if') {
                return requiredIf(ruleName);
            } else if (ruleName === 'required_unless') {
                return requiredUnless(ruleName);
            } else if (ruleName === 'email') {
                return email;
            } else if (ruleName === 'numeric') {
                return numeric;
            } else if (ruleName === 'alpha') {
                return alpha;
            } else if (ruleName === 'alpha_num') {
                return alphaNum;
            } else if (ruleName === 'integer') {
                return integer;
            } else if (ruleName === 'decimal') {
                return decimal;
            } else if (ruleName === 'between') {
                const [min, max] = ruleName.split(':')[1].split(',');
                return between([min, max]);
            } else if (ruleName === 'accepted') {
                return sameAs(true);
            } else if (ruleName.includes('before_or_equal')) {
                return this.dateBefore;
            } else if (ruleName.includes('after_or_equal')) {
                return this.dateAfter;
            } else {
                return null; // Возвращаем null для неизвестных правил
            }
        },

        dateAfter() {
            const dateFrom = moment(this.formModel.date_from, 'DD.MM.YYYY');
            const dateTo = moment(this.formModel.date_to, 'DD.MM.YYYY');
            return dateTo.isSameOrAfter(dateFrom);
        },
        dateBefore() {
            const dateFrom = moment(this.formModel.date_from, 'DD.MM.YYYY');
            const dateTo = moment(this.formModel.date_to, 'DD.MM.YYYY');
            return dateFrom.isSameOrBefore(dateTo);
        },

        processFields(fields, parentPath, validationRules, validationMessages) {
            for (const key in fields) {
                const field = fields[key];
                const path = key;
                if (field.rules) {
                    validationRules[path] = {};
                    validationMessages[path] = {};
                    for (const ruleKey in field.rules) {
                        if (ruleKey && field.rules[ruleKey]) {
                            const rule = field.rules[ruleKey];
                            const ruleParts = rule.split(':');
                            const ruleName = ruleParts[0];
                            const ruleFunction = this.createRule(rule, ruleParts[1]); // Передаем имя и значение правила
                            validationRules[path][ruleKey] = ruleFunction; // Записываем функцию правила
                            if (field.messages && field.messages[ruleName]) {
                                if (!validationMessages[path]) {
                                    validationMessages[path] = {}; // Создаем объект сообщений, если его нет
                                }
                                validationMessages[path][ruleName] = helpers.withMessage(field.messages[ruleName], ruleFunction); // Записываем сообщение с правильным ключом
                            }
                        }
                    }
                } else if (typeof field === 'object') {
                    this.processFields(field, path, validationRules, validationMessages);
                }
            }
        },

        extractValidationRulesAndMessages(fields) {
            const validationRules = {};
            const validationMessages = {};

            this.processFields(fields, '', validationRules, validationMessages);
            return {rules: validationRules, messages: validationMessages};
        },

        generateFormModel(form) {
            const model = {};
            for (const key in form) {
                if (key.includes('fields_')) {
                    Object.assign(model, form[key].value);
                } else {
                    model[key] = form[key].value;
                }
            }
            return model;
        },
    },
    validations() {
        if (this.validations) {
            return {
                formModel: {
                    ...this.validations
                }
            }
        }
    }
}
</script>
