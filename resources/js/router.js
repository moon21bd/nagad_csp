import Vue from "vue";
import Router from "vue-router";
import AdminLayout from "./views/admin/layout/index.vue";

import defaultRoutes from "./routers/default";
import bulkTicketsRoutes from "./routers/bulk-tickets";
import sbadminRoutes from "./routers/sbadmin";
import dndusersRoutes from "./routers/dndusers";
import emailConfigRoutes from "./routers/emailconfig";
import rolesPermissions from "./routers/roles-permissions";
import userRoutes from "./routers/users";
import callTypes from "./routers/call-types";
import callCategory from "./routers/call-category";
import callSubCategory from "./routers/call-sub-category";
import groups from "./routers/groups";
import requiredConfigs from "./routers/required-fields";
import serviceTypeConfigs from "./routers/service-type-configs";
import notifications from "./routers/notification";
import tickets from "./routers/tickets";
import store from "./store";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        ...defaultRoutes,
        ...bulkTicketsRoutes,
        ...sbadminRoutes,
        ...dndusersRoutes,
        ...emailConfigRoutes,
        ...userRoutes,
        ...callTypes,
        ...groups,
        ...callCategory,
        ...callSubCategory,
        ...rolesPermissions,
        ...requiredConfigs,
        ...serviceTypeConfigs,
        ...notifications,
        ...tickets,
    ],
});

// List of routes to be excluded from using AdminLayout
const excludedRoutes = [
    "/login",
    "/login/:user_id?",
    "/register",
    "/forgot-password",
    "/reset/:token",
    "/verify/user/:id",
];

import axios from "./axios";
async function checkPermission(permission) {
    const response = await axios.post("/check-permission", { permission });
    return response.data.allowed;
}

router.beforeEach(async (to, from, next) => {
    const isAuthenticated = store.getters["auth/authenticated"];

    // Apply default layout if no layout is defined and the route is not excluded
    if (!to.meta.layout && !excludedRoutes.includes(to.path)) {
        to.meta.layout = AdminLayout;
    }

    // If the user is authenticated and tries to access the login page, redirect to home
    if (to.path === "/login" && isAuthenticated) {
        return next({ path: "/" });
    }

    // Check if the route requires authentication
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!isAuthenticated) {
            // If the user is not authenticated, redirect to login
            return next({
                path: "/login",
                query: { redirect: to.fullPath },
            });
        }
    }

    // WILL BE OPEN THIS LATER AFTER GETTING ALL OK FOR ROUTER PERMISSION CHECKS.
    // If the route requires a specific permission
    console.log("to.meta.requiresPermission", to.meta.requiresPermission);
    if (to.meta.requiresPermission) {
        await store.dispatch("permissions/fetchPermissions");

        const userRoles = store.getters["permissions/userRoles"];
        const isAdmin =
            userRoles.includes("admin") || userRoles.includes("superadmin");

        if (isAdmin) {
            // If the user is Admin or Super Admin, allow access without permission check
            return next();
        }
        if (
            store.getters["permissions/hasPermission"](
                to.meta.requiresPermission
            )
        ) {
            // User has the required permission, proceed to the route
            return next();
        } else {
            // User doesn't have the required permission, redirect to forbidden page
            return next("/forbidden");
        }
    }

    // If no authentication or permission is required, or all checks passed
    next();
});

export default router;
