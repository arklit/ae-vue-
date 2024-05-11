<template>
    <label :class="labelClassName">{{ label }}</label>
    <v-select :class="className" :options="options" v-model="selectedValue" @input="onInput"></v-select>
    <span v-if="errors" :class="errorClass ?? 'form-error'" v-show="errors.$dirty">{{ errors.$errors[0]?.$message }}</span>
</template>

<script>
import vSelect from 'vue-select';
import { commonProps } from '../../props.js';
export default {
    components: {
        'v-select': vSelect,
    },
    props: commonProps,
    emits: ['update:modelValue'],
    data() {
        return {
            selectedValue: this.modelValue,
            options: []
        };
    },
    mounted() {
        this.getOptions();
    },
    watch: {
        modelValue(newValue) {
            this.selectedValue = newValue;
        },
        selectedValue(newValue) {
            this.$emit('update:modelValue', newValue);
        }
    },
    methods: {
        onInput(event) {
            this.selectedValue = event;
        },

        async getOptions() {
            const response = await axios.get('/ajax/get-options');
            this.options = response.data;
        }
    }
}
</script>

<style scoped>
.v-select {
    width: 100%;
}
</style>
