import axios from "../axios";

export default {
    namespaced: true,
    state: {
        userRoles: [],
        userPermissions: [],
    },
    getters: {
        userRoles: (state) => state.userRoles,
        userPermissions: (state) => state.userPermissions,
        hasRole: (state) => (roleNames) => {
            const rolesArray = roleNames.split("|");
            return rolesArray.some((role) => state.userRoles.includes(role));
        },
        hasPermission: (state) => (permissionName) =>
            state.userPermissions.includes(permissionName),
    },
    actions: {
        async fetchPermissions({ commit }) {
            try {
                const response = await axios.get("/userPermissions");
                // console.log("getCurrentUserPermissions", response.data);
                commit("SET_USER_ROLES", response.data.roles);
                commit("SET_USER_PERMISSIONS", response.data.permissions);
            } catch (error) {
                console.error("Failed to fetch permissions:", error);
            }
        },
    },
    mutations: {
        SET_USER_PERMISSIONS(state, permissions) {
            // console.log("SET_PERMISSIONS", permissions);
            state.userPermissions = permissions;
        },
        SET_USER_ROLES(state, roles) {
            // console.log("SET_ROLES", roles);
            state.userRoles = roles;
        },
    },
    created() {
        this.fetchPermissions(); // Fetch permissions on component creation
    },
};
