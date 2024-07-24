<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'roles-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Role</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="saveRole">
                            <div class="form-group">
                                <label class="control-label">Role Name</label>
                                <input
                                    class="form-control"
                                    v-model="formData.name"
                                    type="text"
                                    required
                                />
                            </div>

                            <span v-if="formErrors.name" class="text-danger">{{
                                formErrors.name[0]
                            }}</span>

                            <button class="btn btn-site" type="submit">
                                Create
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";
import Vue from "vue";

export default {
    data() {
        return {
            isLoading: false,
            formData: {
                name: "",
            },
            formErrors: [],
        };
    },

    methods: {
        async handleSubmit() {
            try {
                this.formErrors = [];
                if (!this.formData.name)
                    this.formErrors.push("Group Name is required.");
                if (!this.formData.status)
                    this.formErrors.push("Status is required.");
                // If there are errors, do not submit the form
                if (this.formErrors.length > 0) return;

                // Submit the form data to the API
                const response = await axios.post("/groups", this.formData);
                console.log("Group created successfully:", response.data);

                // Clear the form data
                this.formData = {
                    name: "",
                    group_id: null,
                };

                // Navigate to the groups list route
                this.$router.push({ name: "groups" });
            } catch (error) {
                console.error("Error creating group:", error);
                if (error.response && error.response.data.errors) {
                    this.formErrors = Object.values(
                        error.response.data.errors
                    ).flat();
                } else {
                    this.formErrors.push(
                        "Failed to create group. Please try again later."
                    );
                }
            }
        },
        async saveRole() {
            this.isError = false;

            if (!this.formData.name) {
                this.errors.name = ["Role name is required"];
                this.isError = true;
            } else {
                this.errors.name = "";
            }

            if (this.isError) {
                return false;
            } else {
                this.errors.name = "";
            }

            this.isLoading = true;

            await axios
                .post("/role/save", this.formData)
                .then(({ data }) => {
                    Vue.prototype.$showToast(data.message, {
                        title: data.message,
                        toaster: `b-toaster-top-right`,
                        variant: "success",
                    });
                    this.$router.push({ name: "roles-index" });
                })
                .catch(({ response: { data } }) => {
                    // console.log("data.errors", data);
                    this.errors = data.errors;
                    // console.log("data.errors");
                    this.isLoading = false;
                });
        },
    },
};
</script>
