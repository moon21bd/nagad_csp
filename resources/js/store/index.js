import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        userPermissions: []
    },
    mutations: {
        setUserPermissions(state, permissions) {
            console.log('setUserPermissions called!')
            state.userPermissions = permissions;
        }
    },
    actions: {
        async fetchUserPermissions({commit}) {
            console.log('fetchUserPermissions called!')
            try {
                const response = await axios.get('/user/permissions');
                commit('setUserPermissions', response.data);
            } catch (error) {
                console.error('Failed to fetch permissions', error);
            }
        }
    }
});
