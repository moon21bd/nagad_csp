require("./bootstrap");

import "./axios";
import ElementUI from "element-ui";
import locale from "element-ui/lib/locale/lang/en";
import "element-ui/lib/theme-chalk/index.css";
import VeeValidate from "vee-validate";
import router from "./router";
import store from "./store";
import trackClick from "./utils/clickTracker";
import ToastPlugin from "./utils/toast";

window.Vue = require("vue").default;

// Registering element-ui
Vue.use(ElementUI, { locale });
// Register click track
Vue.prototype.$trackClick = trackClick;
// Register vee validate
Vue.use(VeeValidate, {
    fieldsBagName: "vee-fields",
    useConstraintAttrs: false,
});

// Register global toast function
Vue.use(ToastPlugin);

// Registering Vue Components
Vue.component("app", require("./App.vue").default);

const app = new Vue({
    router,
    store,
    el: "#app",
});
