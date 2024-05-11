// props.js
export const commonProps = {
    id: String,
    modelValue: String|Object,
    label: String,
    type: String,
    placeholder: String,
    name: String,
    className: String,
    labelClassName: String,
    value: String|Boolean,
    errors: Object,
    errorClass: String,
    mask: String,
    accept: String, // Добавляем новый пропс accept для указания допустимых типов файлов
    multiple: {
        type: Boolean,
        default: false
    } // Добавляем новый пропс multiple для возможности выбора нескольких файлов
};
