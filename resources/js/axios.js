import axios from 'axios';
import store from './vuex'; // Adjust the path to your Vuex store
import router from './router'; // Adjust the path to your Vue router

axios.defaults.baseURL = "/api/"; // Change this if your API URL differs
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');

// Adding Axios Response Interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            store.dispatch('auth/logout').then(() => {
                router.push({ name: 'login' });
            });
        }
        return Promise.reject(error);
    }
);

export default axios;


/*import axios from "axios";

axios.defaults.baseURL = "/api/"; // change this if you want to use a different url for APIs
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');*/
