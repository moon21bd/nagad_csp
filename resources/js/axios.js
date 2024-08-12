import axios from "axios";
import store from "./store";
import router from "./router";
import Vue from "vue";
// import renewToken from "./utils/tokenService";

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

axios.defaults.baseURL = "/api/";
axios.defaults.withCredentials = true;

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken.content;
} else {
    // console.error("CSRF token not found");
}

axios.defaults.headers.common["Authorization"] =
    "Bearer " + localStorage.getItem("token");

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 419) {
            // Handle CSRF token mismatch
            if (
                confirm(
                    "Your session has expired. Please refresh the page to continue."
                )
            ) {
                window.location.reload();
            }
        }
        return Promise.reject(error);
    }
);

export default axios;
