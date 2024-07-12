import axios from '../axios'
import router from '../router'
import {BToast} from 'bootstrap-vue'

export default {
    namespaced: true,
    state: {
        authenticated: false,
        user: {}
    },
    getters: {
        authenticated(state) {
            return state.authenticated
        },
        user(state) {
            return state.user
        }
    },
    mutations: {
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value
        },
        SET_USER(state, value) {
            state.user = value
        }
    },
    actions: {
        login({commit, dispatch}) {
            return axios.post('/user').then((response) => {
                console.log('RESPONSE: ', response);
                const data = response.data;
                console.log('USER-DATA: ', data);
                commit('SET_USER', data)
                commit('SET_AUTHENTICATED', true)
                router.push({name: '/'})
            }).catch((error) => {
                console.error('LOGIN ERROR: ', error);
                if (error.response) {
                    const data = error.response.data;
                    console.error('ERROR RESPONSE DATA: ', data);
                    dispatch('showErrorToast', data.message) // Show error toast
                } else {
                    console.error('ERROR: ', error.message);
                    dispatch('showErrorToast', 'An unexpected error occurred.') // Show generic error toast
                }
                commit('SET_USER', {})
                commit('SET_AUTHENTICATED', false)
            })
        },
        logout({commit}) {
            commit('SET_USER', {})
            commit('SET_AUTHENTICATED', false)
        },
        showErrorToast(_, message) {
            this.$bvToast.toast(message, {
                title: 'Error',
                variant: 'danger',
                solid: true
            })
        }
    }
}


/*
import axios from '../axios'
import router from '../router'

export default {
    namespaced: true,
    state: {
        authenticated: false,
        user: {}
    },
    getters: {
        authenticated(state) {
            return state.authenticated
        },
        user(state) {
            return state.user
        }
    },
    mutations: {
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value
        },
        SET_USER(state, value) {
            state.user = value
        }
    },
    actions: {
        login({commit}) {
            return axios.post('/user').then(({data}) => {
                console.log('USER-DATA: ', data);
                commit('SET_USER', data)
                commit('SET_AUTHENTICATED', true)
                router.push({name: '/'})
            }).catch(({response: {data}}) => {
                commit('SET_USER', {})
                commit('SET_AUTHENTICATED', false)
            })
        },
        logout({commit}) {
            commit('SET_USER', {})
            commit('SET_AUTHENTICATED', false)
        }
    }
}
*/
