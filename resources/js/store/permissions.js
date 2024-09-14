import axios from "../axios";

export default {
    namespaced: true,
    state: {
        userRoles: [],
        // userPermissions: [],
        userPermissions: [], // User-specific permissions
        rolePermissions: [], // Role-based permissions
    },
    getters: {
        allPermissions(state) {
            return [
                ...new Set([
                    ...state.userPermissions,
                    ...state.rolePermissions,
                ]),
            ];
        },
        hasPermission: (state, getters) => (permission) => {
            return getters.allPermissions.includes(permission);
        },
        userRoles: (state) => state.userRoles,
        userPermissions: (state) => state.userPermissions,
        hasRole: (state) => (roleNames) => {
            const rolesArray = roleNames.split("|");
            return rolesArray.some((role) => state.userRoles.includes(role));
        },
        /* hasPermission: (state) => (permissionName) =>
            state.userPermissions.includes(permissionName), */
    },
    actions: {
        async fetchPermissions({ commit }) {
            try {
                const { data } = await axios.get("/userPermissions");
                console.log("getCurrentUserPermissions", data);
                commit("SET_USER_ROLES", data.roles);
                commit("SET_USER_PERMISSIONS", data.userPermissions);
                commit("SET_ROLE_PERMISSIONS", data.rolePermissions);

                // commit("SET_USER_PERMISSIONS", data.permissions);
            } catch (error) {
                console.error("Failed to fetch permissions:", error);
            }
        },
    },
    mutations: {
        SET_USER_PERMISSIONS(state, permissions) {
            state.userPermissions = permissions;
        },
        SET_ROLE_PERMISSIONS(state, permissions) {
            state.rolePermissions = permissions;
        },
        SET_USER_ROLES(state, roles) {
            state.userRoles = roles;
        },
    },
    created() {
        this.fetchPermissions();
    },
};
