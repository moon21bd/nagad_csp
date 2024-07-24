<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0">Permissions Assign</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <form
                ref="PermissionForm"
                action="javascript:void(0)"
                method="post"
            >
                <div class="card-body">
                    <div class="permissions-assign">
                        <div class="permissions-assign-box">
                            <h4>
                                <label class="checkbox"
                                    ><input
                                        type="checkbox"
                                        name="permissions"
                                    /><span class="checkmark"></span>
                                    User
                                </label>
                            </h4>
                            <ul>
                                <li
                                    v-for="(item, key) in availablePermissions"
                                    :key="key"
                                >
                                    <label class="checkbox" :for="item.name">
                                        <input
                                            type="checkbox"
                                            :value="item.id"
                                            :checked="
                                                role.permissions.indexOf(
                                                    item.id
                                                ) !== -1
                                            "
                                            v-model="role.permissions"
                                        />

                                        <span class="checkmark"></span
                                        >{{ item.name }}</label
                                    >
                                </li>
                            </ul>

                            <!-- <div class="d-flex">
                                <div class="row">
                                    <div
                                        class="col-4 checkbox-inline"
                                        v-for="(permission, key) in permissions"
                                        :key="key"
                                    >
                                        <label :for="permission.name" class="">
                                            <input
                                                type="checkbox"
                                                :value="permission.id"
                                                :checked="
                                                    role.rolePermissions.indexOf(
                                                        permission.id
                                                    ) !== -1
                                                "
                                                v-model="role.rolePermissions"
                                            />
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "PermissionsAssign",
    data() {
        return {
            isLoading: false,
            role: {
                // id: "",
                // name: "",
                permissions: [],
            },
            availablePermissions: [],
            rolePermissions: [],
            selectedPermissions: [],
        };
    },
    methods: {
        fetchPermissions() {
            console.log("fetchPermissions called");
            this.isLoading = true;
            axios
                .get("/permissions")
                .then((response) => {
                    console.log("response.data", response.data.data);
                    this.availablePermissions = response.data.data;
                })
                .catch((error) => {
                    console.error("Error fetching permissions:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        fetchRolePermissions(roleId) {
            this.isLoading = true;
            axios
                .get(`/roles/${roleId}/permissions`)
                .then((response) => {
                    this.rolePermissions = response.data;
                    this.selectedPermissions = this.rolePermissions.map(
                        (p) => p.name
                    );
                })
                .catch((error) => {
                    console.error("Error fetching role permissions:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        savePermissions() {
            const roleId = this.$route.params.roleId; // Get roleId from route parameters or other sources
            this.isLoading = true;
            axios
                .post(`/roles/${roleId}/permissions`, {
                    //permissions: this.selectedPermissions,
                }) // Endpoint to save permissions
                .then((response) => {
                    console.log("Permissions updated successfully");
                })
                .catch((error) => {
                    console.error("Error updating permissions:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    },
    mounted() {
        console.log("role", this.role);
        // const roleId = this.$route.params.roleId; // Get roleId from route parameters or other sources
        this.fetchPermissions();
        //this.fetchRolePermissions(roleId);
    },
};
</script>
