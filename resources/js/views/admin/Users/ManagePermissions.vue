<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Assign Permissions</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="Loading" />
            </div>
            <form @submit.prevent="updateUserPermissions">
                <div class="card-body">
                    <div class="form-group">
                        <label for="userName">User Name</label>
                        <input
                            type="text"
                            id="userName"
                            v-model="userName"
                            class="form-control"
                            disabled
                        />
                    </div>
                    <div class="permissions-assign">
                        <div
                            class="permissions-assign-box"
                            v-for="(permissions, category) in permissionUsers"
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
                                        {{ permission.display_name }}
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
    name: "ManagePermissions",
    data() {
        return {
            isLoading: false,
            id: this.$route.params.id,
            permissions: [],
            userName: "",
            selectedPermissions: [],
            errors: {},
        };
    },
    computed: {
        hasErrors() {
            return Object.keys(this.errors).length > 0;
        },
        permissionUsers() {
            return this.permissions.reduce((users, permission) => {
                const category = permission.name.split("-")[0];
                if (!users[category]) users[category] = [];
                users[category].push(permission);
                return users;
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
        async fetchUser(id) {
            this.isLoading = true;
            try {
                const { data: userData } = await axios.get(`/user/${id}`);
                console.log("User data:", userData);
                // return;

                this.userName = userData.data.name || "";
                console.log("userData.permission", userData.permissions);
                // Ensure groupData.role.rolePermissions is an array
                if (Array.isArray(userData.permissions)) {
                    const assignedPermissions = new Set(
                        userData.permissions.map((p) => p.name)
                    );
                    this.selectedPermissions = this.permissions
                        .map((p) => p.name)
                        .filter((name) => assignedPermissions.has(name));
                } else {
                    console.error(
                        "User permissions data is not in the expected format:"
                    );
                }
            } catch (error) {
                console.error("Error fetching user permissions data:", error);

                this.$showToast("Error fetching user permissions data", {
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
                    await this.fetchUser(this.$route.params.id);
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

            if (this.selectedPermissions.length === 0) {
                this.$showToast("At least one permission must be selected.", {
                    type: "error",
                });
                return false;
            }
            return Object.keys(this.errors).length === 0;
        },
        async updateUserPermissions() {
            if (!this.validate()) return;

            this.isLoading = true;
            try {
                const { data } = await axios.post(
                    `/user/${this.$route.params.id}/permissions`,
                    {
                        permissions: this.selectedPermissions,
                    }
                );

                this.$showToast(data.message, {
                    type: data.type,
                });
                this.$router.push({ name: "user-index" });
            } catch (error) {
                console.error("Error updating user:", error);

                this.$showToast("Error updating user permissions", {
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
