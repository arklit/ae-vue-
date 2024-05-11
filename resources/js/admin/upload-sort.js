// Функция для обновления скрытых инпутов
function updateHiddenInputsOrder(dropzonePreviews, element) {
    let newChildrenWithDataFileId = dropzonePreviews.querySelectorAll('[data-file-id]');
    newChildrenWithDataFileId.forEach((child, index) => {
        let hiddenInputs = element.querySelectorAll('input[type="hidden"][class*="files-"]');
        hiddenInputs.forEach((input, inputIndex) => {
            if (index === inputIndex) {
                input.value = child.dataset.fileId;
                input.classList.remove([...input.classList].filter(className => className.startsWith('files-'))[0]);
                input.classList.add(`files-${child.dataset.fileId}`);
            }
        });
    });
}

// Функция для поиска элементов с контроллером загрузки
function findElementsWithUploadController(elements) {
    elements.forEach((element) => {
        let dropzonePreviews = element.querySelector('.dropzone-previews');
        if (dropzonePreviews) {
            let observer = new MutationObserver(() => {
                updateHiddenInputsOrder(dropzonePreviews, element);
            });
            observer.observe(dropzonePreviews, {childList: true});
        }
    });
}

// Функция для обработки мутаций
function handleMutation(mutationsList) {
    for (const mutation of mutationsList) {
        if (mutation.type === 'childList') {
            const elements = document.querySelectorAll('[data-controller="upload"]');
            if (elements.length > 0) {
                findElementsWithUploadController(elements);
                observer.disconnect();
                break;
            }
        }
    }
}

// Создание наблюдателя мутаций
const observer = new MutationObserver(handleMutation);

// Наблюдение за событием turbo:load
document.addEventListener('turbo:load', () => {
    observer.observe(document.documentElement, {childList: true, subtree: true});
});

// Наблюдение за событием DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    observer.observe(document.documentElement, {childList: true, subtree: true});
});
