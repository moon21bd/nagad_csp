import axios from 'axios';
import store from './store';
import router from './router';
import Vue from 'vue';

// Adding Axios Response Interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        console.log('ERROR::', error)
        if (error.response && error.response.status === 401) {
            localStorage.removeItem("token");
            store.dispatch("user", null).then(() => {
                console.log('Hello to Login');
                router.push({name: 'login'});
            });
        }
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    response => response,
    error => {

        if (error.response.status === 403) {
            // console.log('error.response.', error.response.data?.error)
            Vue.prototype.$showToast('You are not authorized to perform this action.', {
                variant: 'error'
            });
        }
        return Promise.reject(error);
    }
);

axios.defaults.baseURL = "/api/";
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');


export default axios;
