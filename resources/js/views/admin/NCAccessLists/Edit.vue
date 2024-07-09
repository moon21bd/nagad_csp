<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/access-lists"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Update Access Name</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="updateAccess">
                            <div class="form-group">
                                <label class="control-label">Access Name</label>
                                <input
                                    class="form-control"
                                    v-model="form.access_name"
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
                                        v-model="form.status"
                                        required
                                    /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="form.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
                            <button class="btn btn-site" type="submit">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            isLoading: false,
            form: {
                access_name: "",
                status: "active",
            },
        };
    },
    async created() {
        try {
            const accessId = this.$route.params.id;
            const response = await axios.get(`/access-lists/${accessId}`);
            this.form = response.data;
        } catch (error) {
            console.error("Error fetching access list:", error);
        }
    },
    methods: {
        async updateAccess() {
            try {
                const accessId = this.$route.params.id;
                const response = await axios.put(
                    `/access-lists/${accessId}`,
                    this.form
                );
                console.log("Access List updated successfully:", response.data);
                this.$router.push({ name: "access-lists" });
            } catch (error) {
                console.error("Error updating access list:", error);
            }
        },
    },
};
</script>
