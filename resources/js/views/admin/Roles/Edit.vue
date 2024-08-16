<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'roles-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Assign Permissions to Role</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="Loading" />
            </div>
            <form @submit.prevent="updateRole">
                <div class="card-body">
                    <div class="form-group">
                        <label for="roleName">Role Name</label>
                        <input
                            type="text"
                            id="roleName"
                            v-model="roleName"
                            class="form-control"
                            placeholder="Enter role name"
                        />
                        <span v-if="hasErrors" class="text-danger">{{
                            errors.name
                        }}</span>
                    </div>
                    <div class="permissions-assign">
                        <div
                            class="permissions-assign-box"
                            v-for="(permissions, category) in permissionGroups"
                            :key="category"
                        >
                            <h4>
                                <label class="checkbox">
                                    <input
                                        type="checkbox"
                                        :name="category"
                                        :id="category"
                                        :checked="areAllSelected(category)"
                                        @change="toggleCategory(category)"
                                    />
                                    <span class="checkmark"></span>
                                    {{ capitalizeCategory(category) }} Manage
                                </label>
                            </h4>
                            <ul>
                                <li
                                    v-for="permission in permissions"
                                    :key="permission.id"
                                >
                                    <label class="checkbox">
                                        <input
                                            type="checkbox"
                                            :name="permission.name"
                                            :id="permission.name"
                                            :checked="
                                                isSelected(permission.name)
                                            "
                                            @change="
                                                togglePermission(
                                                    permission.name
                                                )
                                            "
                                        />
                                        <span class="checkmark"></span>
                                        {{ getLabel(permission.name) }}
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="btn btn-site"
                        :disabled="isLoading"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "Edit",
    data() {
        return {
            isLoading: false,
            permissions: [],
            roleName: "",
            selectedPermissions: [],
            errors: {},
        };
    },
    computed: {
        hasErrors() {
            return Object.keys(this.errors).length > 0;
        },
        permissionGroups() {
            return this.permissions.reduce((groups, permission) => {
                const category = permission.name.split("-")[0];
                if (!groups[category]) groups[category] = [];
                groups[category].push(permission);
                return groups;
            }, {});
        },
    },
    methods: {
        capitalizeCategory(text) {
            return text.charAt(0).toUpperCase() + text.slice(1);
        },
        getLabel(permissionName) {
            return permissionName
                .split("-")
                .map((part, index) =>
                    index === 0
                        ? part.charAt(0).toUpperCase() + part.slice(1)
                        : part
                )
                .join(" ");
        },
        isSelected(permissionName) {
            return this.selectedPermissions.includes(permissionName);
        },
        togglePermission(permissionName) {
            this.selectedPermissions = this.isSelected(permissionName)
                ? this.selectedPermissions.filter((p) => p !== permissionName)
                : [...this.selectedPermissions, permissionName];
        },
        areAllSelected(category) {
            const categoryPermissions = this.permissions
                .filter((p) => p.name.startsWith(category))
                .map((p) => p.name);
            return categoryPermissions.every((permission) =>
                this.isSelected(permission)
            );
        },
        toggleCategory(category) {
            const categoryPermissions = this.permissions
                .filter((p) => p.name.startsWith(category))
                .map((p) => p.name);

            this.selectedPermissions = this.areAllSelected(category)
                ? this.selectedPermissions.filter(
                      (p) => !categoryPermissions.includes(p)
                  )
                : [
                      ...new Set([
                          ...this.selectedPermissions,
                          ...categoryPermissions,
                      ]),
                  ];
        },
        async fetchRole(roleId) {
            this.isLoading = true;
            try {
                const { data: roleData } = await axios.get(`/role/${roleId}`);
                console.log("Role data:", roleData); // Log the entire response

                this.roleName = roleData.role?.name || "";

                // Ensure roleData.role.rolePermissions is an array
                if (Array.isArray(roleData.role?.rolePermissions)) {
                    const assignedPermissions = new Set(
                        roleData.role.rolePermissions.map((p) => p.name)
                    );
                    this.selectedPermissions = this.permissions
                        .map((p) => p.name)
                        .filter((name) => assignedPermissions.has(name));
                } else {
                    console.error(
                        "Role permissions data is not in the expected format:",
                        roleData.role?.rolePermissions
                    );
                }
            } catch (error) {
                console.error("Error fetching role data:", error);

                this.$showToast("Error fetching role data", {
                    type: "error",
                });
            } finally {
                this.isLoading = false;
            }
        },
        async fetchPermissions() {
            this.isLoading = true;
            try {
                const { data } = await axios.get("/permissions");
                this.permissions = data.data || [];

                if (this.$route.params.id) {
                    await this.fetchRole(this.$route.params.id);
                }
            } catch (error) {
                console.error("Error fetching permissions:", error);

                this.$showToast("Error fetching permissions", {
                    type: "error",
                });
            } finally {
                this.isLoading = false;
            }
        },
        validate() {
            this.errors = {};
            if (!this.roleName) {
                this.errors.name = "Role name is required.";
            } else if (this.roleName.length < 3) {
                this.errors.name = "Role name must be at least 3 characters.";
            }
            if (this.selectedPermissions.length === 0) {
                this.$showToast("At least one permission must be selected.", {
                    type: "error",
                });
                return false;
            }
            return Object.keys(this.errors).length === 0;
        },
        async updateRole() {
            if (!this.validate()) return;

            this.isLoading = true;
            try {
                const { data } = await axios.put(
                    `/role/${this.$route.params.id}`,
                    {
                        name: this.roleName,
                        permissions: this.selectedPermissions,
                    }
                );

                this.$showToast(data.message, {
                    type: "error",
                });
                this.$router.push({ name: "roles-index" });
            } catch (error) {
                console.error("Error updating role:", error);

                this.$showToast("Error updating role", {
                    type: "error",
                });
            } finally {
                this.isLoading = false;
            }
        },
    },
    created() {
        this.fetchPermissions();
    },
};
</script>
