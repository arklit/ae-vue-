const toggleModalShow = (state) => {
    state.modalOpen = !state.modalOpen;
    if (state.modalOpen) {
        document.documentElement.style.overflow = 'hidden';
    } else {
        document.documentElement.style.overflow = 'auto';
    }
}
const setModalContent = (state, payload) => {
    state.modalContent = payload;
}
export default {
    toggleModalShow,
    setModalContent
}
