<template>
    <label :for="id" :class="labelClassName" v-html="label"></label>
    <textarea :id="id" v-mask="mask" :class="className" :placeholder="placeholder" :name="name" :value="internalValue" @input="onInput"></textarea>
    <span v-if="errors" :class="errorClass ?? 'form-error'" v-show="errors.$dirty">{{ errors.$errors[0]?.$message }}</span>
</template>

<script>
import VueMask from 'v-mask';
import { commonProps } from '../../props.js';
export default {
    props: commonProps,
    emits: ['update:modelValue'],
    components: {
        VueMask
    },
    computed: {
        internalValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    methods: {
        onInput(event) {
            this.internalValue = event.target.value;
        },
        onCheckboxChange(event) {
            this.internalValue = event.target.checked;
        }
    }
}
</script>
