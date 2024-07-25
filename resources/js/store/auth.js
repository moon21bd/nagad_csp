const state = {
    authenticated: false,
    user: null,
    token: null,
    tokenExpiration: null,
};

const getters = {
    authenticated: (state) => state.authenticated,
    user: (state) => state.user,
    token: (state) => state.token,
    tokenExpiration: (state) => state.tokenExpiration,
};

const mutations = {
    SET_AUTHENTICATED(state, authenticated) {
        state.authenticated = authenticated;
    },
    SET_USER(state, user) {
        state.user = user;
    },
    SET_TOKEN(state, token) {
        state.token = token;
        state.tokenExpiration = new Date().getTime() + 60 * 60 * 1000; // Token valid for 60 minutes
    },
    CLEAR_TOKEN(state) {
        state.token = null;
        state.tokenExpiration = null;
    },
};

const actions = {
    setUser({ commit }, user) {
        commit("SET_AUTHENTICATED", true);
        commit("SET_USER", user);
    },
    setToken({ commit }, token) {
        commit("SET_TOKEN", token);
    },
    logout({ commit }) {
        commit("SET_AUTHENTICATED", false);
        commit("SET_USER", null);
        commit("CLEAR_TOKEN");
        localStorage.removeItem("token");
        localStorage.removeItem("vuex");
        delete axios.defaults.headers.common["Authorization"];
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
