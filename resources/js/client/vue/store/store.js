import { createStore } from "vuex";
import { state } from "./state.js";
import mutations from "./mutations.js";

export const store = createStore({
    state: () => state,
    mutations,
})
