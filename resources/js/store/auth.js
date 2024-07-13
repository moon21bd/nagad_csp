const state = {
    authenticated: false,
    user: null,
};

const getters = {
    authenticated: state => state.authenticated,
    user: state => state.user,
};

const mutations = {
    SET_AUTHENTICATED(state, authenticated) {
        state.authenticated = authenticated;
    },
    SET_USER(state, user) {
        state.user = user;
    },
};

const actions = {
    setUser({ commit }, user) {
        commit('SET_AUTHENTICATED', true);
        commit('SET_USER', user);
    },
    logout({ commit }) {
        commit('SET_AUTHENTICATED', false);
        commit('SET_USER', null);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
