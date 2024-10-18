import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import auth from "./auth";
import permissions from "./permissions";
import sessionStatus from "./sessionStatus";

Vue.use(Vuex);

export default new Vuex.Store({
    plugins: [createPersistedState()],
    modules: {
        auth,
        permissions,
        sessionStatus,
    },
});
