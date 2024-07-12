import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../vuex'
import AdminLayout from "../views/admin/layout/index.vue";

Vue.use(VueRouter)

/* Guest Component */
// const Login = () => import('../components/backoffice/Login.vue' /* webpackChunkName: "resource/js/components/login" */)
// const Register = () => import('../components/backoffice/Register.vue' /* webpackChunkName: "resource/js/components/register" */)
const Forgetpassword = () => import('../components/backoffice/Forgetpassword.vue' /* webpackChunkName: "resource/js/components/register" */)
const Resetpassword = () => import('../components/backoffice/Resetpassword.vue' /* webpackChunkName: "resource/js/components/register" */)
const Home = () => import('../components/backoffice/Home.vue' /* webpackChunkName: "resource/js/components/Home" */)
/* Guest Component */

/* Authenticated Component */
// const Dashboard = () => import('../components/backoffice/Dashboard.vue' /* webpackChunkName: "resource/js/components/dashboard" */)
const Profile = () => import('../components/backoffice/Profile.vue')
const Roles = () => import('../components/backoffice/Roles.vue')
const Permissions = () => import('../components/backoffice/Permissions.vue')
const Users = () => import('../components/backoffice/Users.vue')
/* Authenticated Component */


const Routes = [
    /*{
        name: "home",
        path: "/",
        component: Home,
        meta: {
            middleware: "guest",
            title: 'home'
        }
    },
    {
        name: "login",
        path: "/login",
        component: Login,
        meta: {
            middleware: "guest",
            title: 'Login'
        }
    },
    {
        name: "register",
        path: "/register",
        component: Register,
        meta: {
            middleware: "guest",
            title: 'Register'
        }
    },*/
    {
        name: "forgetpassword",
        path: "/forgetpassword",
        component: Forgetpassword,
        meta: {
            middleware: "guest",
            layout: AdminLayout,
            title: 'Forget Password',
        }
    },
    {
        name: "reset-password",
        path: "/reset-password/:token",
        component: Resetpassword,
        meta: {
            middleware: "guest",
            layout: AdminLayout,
            title: 'Reset Password'
        }
    },
    /*{
        name: "dashboard",
        path: "/admin/dashboard",
        component: Dashboard,
        meta: {
            middleware: "auth",
            layout: AdminLayout,
            title: 'Dashboard'
        }
    },*/
    {
        name: "profile",
        path: "/admin/profile",
        component: Profile,
        meta: {
            middleware: "auth",
            layout: AdminLayout,
            title: 'Profile'
        }
    },
    {
        name: "roles",
        path: "/admin/roles",
        component: Roles,
        meta: {
            middleware: "auth",
            layout: AdminLayout,
            title: 'Roles'
        }
    },
    {
        name: "permissions",
        path: "/admin/permissions",
        component: Permissions,
        meta: {
            middleware: "auth",
            layout: AdminLayout,
            title: 'Permissions'
        }
    },
    {
        name: "users",
        path: "/admin/users",
        component: Users,
        meta: {
            middleware: "auth",
            layout: AdminLayout,
            title: 'Users'
        }
    }
]

var router = new VueRouter({
    mode: 'history',
    routes: Routes
})

router.beforeEach((to, from, next) => {
    console.log("Navigating to:", to.fullPath);
    console.log("Authentication status:", store.state.auth.authenticated);

    const isAuthenticated = store.state.auth.authenticated;

    if (to.meta.middleware === "guest") {
        if (isAuthenticated) {
            next({name: "home"});
        } else {
            next();
        }
    } else {
        if (!isAuthenticated) {
            next({name: "login"}); // Redirect to login if not authenticated
        } else {
            next();
        }
    }
});


export default router
