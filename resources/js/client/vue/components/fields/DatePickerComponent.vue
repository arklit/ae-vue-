<template>
    <label :for="id" :class="labelClassName" v-html="label"></label>
    <input :id="id" autocomplete="off" v-on:keydown.prevent :class="className + ' base-date-input'" :type="type" :placeholder="placeholder" :name="name" :value="internalValue"/>
    <span v-if="errors" :class="errorClass ?? 'form-error'" v-show="errors.$dirty">{{ errors.$errors[0]?.$message }}</span>
</template>

<script>

import AirDatepicker from "air-datepicker";
import 'air-datepicker/air-datepicker.css';
import { commonProps } from '../../props.js';
export default {
    props: commonProps,
    emits: ['update:modelValue'],
    data() {
        return {
            datePicker: null
        };
    },
    mounted() {
        const today = new Date();
        this.datePicker = new AirDatepicker('#'+this.id, {
            autoClose: true,
            // minDate: today,
            onShow: () => {
            },
            onChangeViewDate: () => {
            },
            onChangeView: () => {
            },
            onSelect: () => {
                const selectedDate = this.datePicker.selectedDates[0];
                const day = selectedDate.getDate().toString().padStart(2, '0');
                const month = (selectedDate.getMonth() + 1).toString().padStart(2, '0');
                const year = selectedDate.getFullYear();

                const formattedDate = `${day}.${month}.${year}`;
                this.onInput({target: {value: formattedDate}});
            },
        });
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
        }
    }
}
</script>
