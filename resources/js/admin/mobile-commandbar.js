window.addEventListener('scroll', function() {
    var wrapper = document.querySelector('.command-bar-wrapper');
    var child = document.querySelector('.command-bar-wrapper .commandbar-layout');
    var wrapperTop = wrapper.getBoundingClientRect().top;

    if (wrapperTop <= 0) {
        child.classList.add('sticky');
    } else {
        child.classList.remove('sticky');
    }
});
