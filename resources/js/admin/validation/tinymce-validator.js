import {setValidationClass, validateElement} from "./validation";

// Функция для проверки TinyMCE
export function validateOnLoad(input) {
    if (!input.classList.contains('tinymce')) {
        return; // Если у инпута нет класса 'tinymce', ничего не делаем
    }

    const checkEditor = setInterval(() => {
        let editor = tinymce.get(input.id); // Получаем объект TinyMCE редактора по его идентификатору
        if (editor) {
            clearInterval(checkEditor); // Останавливаем интервал, если редактор инициализирован или достигнуто максимальное количество попыток
            const editorContainer = input.closest('[data-controller="tinymce"]'); // Находим ближайший родительский элемент редактора TinyMCE с атрибутом 'data-controller="tinymce"'
            const toxContainer = editorContainer.querySelector('.tox-tinymce'); // Находим элемент "tox-tinymce" внутри родительского элемента

            function validateTinyMCEAndSetInputValue() {
                input.value = editor.getContent(); // Устанавливаем значение входного элемента равным содержимому редактора TinyMCE
            }

            validateTinyMCEAndSetInputValue()

            // Назначаем обработчики событий 'keyup' и 'change' на редакторе TinyMCE
            editor.on('keyup', validateTinyMCEAndSetInputValue);
            editor.on('change', validateTinyMCEAndSetInputValue);
        }
    }, 1000);
}

export function validateTinyMce(input) {
    let editor = tinymce.get(input.id); // Получаем объект TinyMCE редактора по его идентификатору
    const editorContainer = input.closest('[data-controller="tinymce"]'); // Находим ближайший родительский элемент редактора TinyMCE с атрибутом 'data-controller="tinymce"'
    const toxContainer = editorContainer.querySelector('.tox-tinymce'); // Находим элемент "tox-tinymce" внутри родительского элемента

    function validateTinyMCEAndSetInputValue() {
        input.value = editor.getContent(); // Устанавливаем значение входного элемента равным содержимому редактора TinyMCE
        setValidationClass(toxContainer, editor.getContent({format: 'text'}).trim() !== '', true);
    }

    validateTinyMCEAndSetInputValue()

    // Назначаем обработчики событий 'keyup' и 'change' на редакторе TinyMCE
    editor.on('keyup', validateTinyMCEAndSetInputValue);
    editor.on('change', validateTinyMCEAndSetInputValue);

}

// Функция для обработки события 'keyup' на элементах TinyMCE
export function TinyMCEKeyUpEvent() {
    const editors = document.querySelectorAll('.tinymce[required]'); // Находим все элементы TinyMCE с атрибутом 'required="required"'
    editors.forEach((input) => {
        validateOnLoad(input); // Вызываем функцию validateTinyMce для каждого элемента
    }); // Вызываем функцию validateElement для каждого элемента
}
