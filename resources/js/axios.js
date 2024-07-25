import axios from "axios";
import store from "./store";
// import store from "@/store";
import router from "./router";
import Vue from "vue";
import renewToken from "./utils/tokenService";

// Adding Axios Response Interceptor For Auto Logout
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        console.log("ERROR::", error.response && error.response.status === 401);
        if (error.response && error.response.status === 401) {
            localStorage.removeItem("token");
            store.dispatch("auth/logout").then(() => {
                console.log("Hello to Login");
                router.push({ name: "login" });
            });
        }
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response.status === 403) {
            // console.log('error.response.', error.response.data?.error)
            Vue.prototype.$showToast(
                "You are not authorized to perform this action.",
                {
                    variant: "error",
                }
            );
        }
        return Promise.reject(error);
    }
);

// Add a request interceptor
/* axios.interceptors.request.use(
    async (config) => {
        console.log("interceptors called", config);
        const token = store.getters["auth/token"];
        if (token) {
            const tokenExpiration = store.getters["auth/tokenExpiration"];
            const now = new Date().getTime();
            if (tokenExpiration - now < 5 * 60 * 1000) {
                // Less than 5 minutes to expiry
                await renewToken();
            }
            console.log(
                "tokenExpiration",
                "before",
                token,
                "after",
                store.getters["auth/token"]
            );
            config.headers[
                "Authorization"
            ] = `Bearer ${store.getters["auth/token"]}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
); */

axios.defaults.baseURL = "/api/";
axios.defaults.headers.common["Authorization"] =
    "Bearer " + localStorage.getItem("token");

export default axios;
