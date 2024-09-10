<!-- <template>
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
</template> -->

<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
            >
                <i class="icon-left"></i>
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
        async fetchUserPermissions() {
            const userId = this.id;
            try {
                const response = await axios.get(`/user/${userId}/permissions`);
                this.selectedPermissions = response.data.permissions;
                console.log(
                    "this.selectedPermissions",
                    this.selectedPermissions
                );
            } catch (error) {
                console.error("Error fetching user permissions:", error);
            }
        },
        capitalizeCategory(text) {
            return text.charAt(0).toUpperCase() + text.slice(1);
        },
        async fetchUser(id) {
            this.isLoading = true;
            try {
                const { data: userData } = await axios.get(`/user/${id}`);
                console.log("User data:", userData); // Check the structure of userData

                if (userData && userData.data) {
                    this.userName = userData.data.name || "";

                    const assignedPermissions = new Set(
                        (userData.data.roles || []).flatMap((role) =>
                            (role.permissions || []).map((p) => p.name)
                        )
                    );

                    console.log("assignedPermissions", assignedPermissions);

                    this.selectedPermissions = this.permissions
                        .map((p) => p.name)
                        .filter((name) => assignedPermissions.has(name));

                    console.log(
                        "selectedPermissions",
                        this.selectedPermissions
                    );
                } else {
                    console.error(
                        "User data or user data structure is incorrect:",
                        userData
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
                if (this.id) {
                    await this.fetchUser(this.id);
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
            return true;
        },
        async updateUserPermissions() {
            if (!this.validate()) return;
            this.isLoading = true;
            try {
                const { data } = await axios.post(
                    `/user/${this.id}/permissions`,
                    {
                        permissions: this.selectedPermissions,
                    }
                );
                this.$showToast(data.message, { type: data.type });
                this.$router.push({ name: "user-index" });
            } catch (error) {
                console.error("Error updating user permissions:", error);
                this.$showToast("Error updating user permissions", {
                    type: "error",
                });
            } finally {
                this.isLoading = false;
            }
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
            return categoryPermissions.every((p) => this.isSelected(p));
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
    },
    created() {
        // this.fetchPermissions();
        this.fetchPermissions().then(() => {
            this.fetchUserPermissions();
        });
        this.fetchUserPermissions();
    },
};
</script>
