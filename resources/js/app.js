/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en';
import 'element-ui/lib/theme-chalk/index.css';
import Toasted from 'vue-toasted';
import './axios';
import router from './router';
import store from './store';
import {ToastPlugin} from 'bootstrap-vue';
import Permissions from './mixins/Permissions';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import VeeValidate from 'vee-validate';

window.Vue = require('vue').default;

Vue.use(Toasted);
Vue.use(ElementUI, {locale});
Vue.use(ToastPlugin);

Vue.use(VeeValidate);

Vue.mixin(Permissions);

// Registering Vue Components
Vue.component('app', require('./App.vue').default);

const app = new Vue({
    router,
    store,
    el: '#app',
    created() {
        //
    }
});
