// router/index.js
import Vue from 'vue';
import Router from 'vue-router';
import store from '@/store';
import Dashboard from '@/views/Dashboard.vue';
import Profile from '@/views/Profile.vue';
import Forbidden from '@/views/Forbidden.vue';

Vue.use(Router);

const routes = [
    { path: '/dashboard', component: Dashboard, name: 'Dashboard' },
    { path: '/profile', component: Profile, name: 'Profile' },
    { path: '/forbidden', component: Forbidden, name: 'Forbidden' },
];

const router = new Router({
    mode: 'history',
    routes
});

router.beforeEach((to, from, next) => {
    const userPermissions = store.state.userPermissions;
    if (userPermissions.some(permission => permission.path === to.path)) {
        next();
    } else {
        next({ name: 'Forbidden' });
    }
});

export default router;
