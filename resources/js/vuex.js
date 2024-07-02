import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

const state = {
    user: null,
    groups: []
};

const store = new Vuex.Store({
    state,
    getters: {
        user: (state) => {
            return state.user;
        },
        groups: state => state.groups

    },
    actions: {
        user: (context, user) => {
            context.commit('user', user);
        },
        async fetchGroups({ commit }) {
            try {
                const response = await axios.get('/groups');
                console.log('Fetched groups:', response.data);
                commit('SET_GROUPS', response.data);
            } catch (error) {
                console.error('Error fetching groups:', error);
                // Optionally handle error state or show a notification
            }
        },
        async createGroup({ commit }, groupData) {
            try {
                const response = await axios.post('/groups', groupData);
                commit('ADD_GROUP', response.data); // Mutate state after successful creation
            } catch (error) {
                console.error('Error creating group:', error);
                // Optionally handle error state or show a notification
            }
        },
        async deleteGroup({ commit }, groupId) {
            try {
                await axios.delete(`/groups/${groupId}`);
                commit('removeGroup', groupId);
            } catch (error) {
                console.error('Error deleting group:', error);
                throw error; // Re-throw the error for component handling
            }
        }
    },
    mutations: {
        user: (state, user) => {
            state.user = user;
        },
        SET_GROUPS(state, groups) {
            console.log('Setting groups:', groups);
            state.groups = groups;
        },
        removeGroup(state, groupId) {
            state.groups = state.groups.filter(group => group.id !== groupId);
        }
    },
    plugins: [createPersistedState()]
});

export default store;
