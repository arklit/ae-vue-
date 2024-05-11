<template>
    <div>
        <label :for="id" :class="labelClassName" v-html="label"></label>
        <span v-if="length > 1">{{ length }}</span>
        <input :id="id" :class="className" type="file" :name="name" :accept="accept" :multiple="multiple" @change="onFileChange">
        <span v-if="errors" :class="errorClass ?? 'form-error'" v-show="errors.$dirty">{{ errors.$errors[0]?.$message }}</span>

        <div v-if="multiple" v-for="(file, index) in downloadedFiles" :key="index">
            <span>{{ file.name }}</span>
            <button @click.prevent="deleteFile(index)">Удалить</button>
        </div>
    </div>
</template>

<script>
import { commonProps } from '../../props.js';

export default {
    props: commonProps,
    emits: ['update:modelValue'],
    data() {
        return {
            length: 0
        }
    },
    computed: {
        downloadedFiles() {
            return this.modelValue || [];
        }
    },
    methods: {
        // Метод для обработки изменений файлов
        onFileChange(event) {
            const files = event.target.files;
            if (files.length === 0) {
                this.$emit('update:modelValue', null); // Очищаем поле, если количество файлов становится нулевым
            } else {
                let updatedFiles;
                if (this.multiple) {
                    updatedFiles = [...this.downloadedFiles, ...files];
                } else {
                    updatedFiles = [files[0]];
                }
                this.$emit('update:modelValue', updatedFiles);
                this.length = updatedFiles.length; // Обновляем значение длины
            }
        },

        // Метод для удаления файла
        deleteFile(index) {
            const updatedFiles = this.downloadedFiles.slice(); // Создаем копию массива
            updatedFiles.splice(index, 1); // Удаляем файл из массива
            this.$emit('update:modelValue', updatedFiles); // Обновляем модель значения
            this.length = updatedFiles.length; // Обновляем значение длины
        }
    }
}
</script>
