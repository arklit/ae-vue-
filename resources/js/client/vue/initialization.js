import {createApp} from 'vue/dist/vue.esm-bundler';
import {store} from "./store/store.js";
import FormBuilder from "./components/FormBuilder.vue";
import { VueMaskDirective } from 'v-mask';
const vMaskV2 = VueMaskDirective;
const vMaskV3 = {
    beforeMount: vMaskV2.bind,
    updated: vMaskV2.componentUpdated,
    unmounted: vMaskV2.unbind
};

if (document.getElementById("app") !== null) {
    const app = createApp({})
        .component('form-builder', FormBuilder)
        .directive('mask', vMaskV3)
        .use(store)
        .mount('#app')
}

/**
 * Добавляем CSRF-защиту
 */
let el = document.querySelector('meta[name="csrf-token"]');
let token = el
    ? el.getAttribute('content')
    : null;
axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
