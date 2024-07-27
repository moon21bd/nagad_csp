/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import ElementUI from "element-ui";
import locale from "element-ui/lib/locale/lang/en";
import "element-ui/lib/theme-chalk/index.css";
import VeeValidate from "vee-validate";
import Toasted from "vue-toasted";
import "./axios";
import Permissions from "./mixins/Permissions";
import router from "./router";
import store from "./store";
window.Vue = require("vue").default;

Vue.use(Toasted);
Vue.use(ElementUI, { locale });
// Vue.use(ToastPlugin);

Vue.use(VeeValidate, {
    fieldsBagName: "vee-fields",
    useConstraintAttrs: false,
});

Vue.mixin(Permissions);

// Register global toast function
Vue.prototype.$showToast = function (message, options = {}) {
    this.$toasted.show(message, {
        theme: options.theme || "toasted-primary",
        position: options.position || "top-right",
        duration: options.duration || 5000,
        type: options.type || "default", // 'default', 'info', 'success', 'error'
        ...options,
    });
};

// Registering Vue Components
Vue.component("app", require("./App.vue").default);

const app = new Vue({
    router,
    store,
    el: "#app",
});
