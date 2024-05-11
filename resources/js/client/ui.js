// переключение класса на чекбоксах
if(document.querySelector('.span-1') !== null) {
    document.querySelectorAll('.span-1')
        .forEach((item) => {
            item.addEventListener('click', (e) => e.currentTarget.classList.toggle('checked'))
        })
}
