import axios from 'axios';
import store from './store'; // Adjust the path to your Vuex store
import router from './router'; // Adjust the path to your Vue router

// Adding Axios Response Interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        console.log('ERROR::', error)
        if (error.response && error.response.status === 401) {
            localStorage.removeItem("token");
            store.dispatch("user", null).then(() => {
                console.log('hello login')
                router.push({ name: 'login' });
            });
        }
        return Promise.reject(error);
    }
);

axios.defaults.baseURL = "/api/"; // Change this if your API URL differs
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');


export default axios;
