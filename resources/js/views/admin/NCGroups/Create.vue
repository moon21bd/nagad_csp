<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/groups"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Group</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="handleSubmit">
                            <div class="form-group">
                                <label class="control-label">Group Name</label>
                                <input
                                    class="form-control"
                                    v-model="formData.name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3"
                                    >Status:</label
                                >
                                <label class="radio mr-2"
                                    ><input
                                        type="radio"
                                        value="active"
                                        v-model="formData.status"
                                        required
                                    /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="formData.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
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

export default {
    data() {
        return {
            isLoading: false,
            formData: {
                name: "",
                status: "active",
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
                    status: "active",
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
    },
};
</script>
