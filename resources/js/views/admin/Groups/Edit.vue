<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'groups-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Group</h1>
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
                                    name="name"
                                    type="text"
                                    v-validate="'required'"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('name')"
                                >
                                    {{ errors.first("name") }}
                                </div>
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
                                Update
                            </button>
                        </form>
                        <ul class="list-group" v-if="formErrors.length">
                            <li
                                class="list-group-item list-group-item-danger"
                                v-for="error in formErrors"
                                :key="error"
                            >
                                {{ error }}
                            </li>
                        </ul>
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
            formData: {
                name: "",
                status: "active",
            },
            formErrors: [],
            id: this.$route.params.id,
        };
    },
    methods: {
        async handleSubmit() {
            const _this = this;
            _this.$validator.validateAll().then(async (result) => {
                if (result) {
                    axios({
                        method: "PUT",
                        url: `/groups/${this.id}`,
                        data: _this.formData,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            _this.isLoading = false;
                            _this.formData = {};
                            Vue.prototype.$showToast(
                                "Group updated successfully.",
                                {
                                    title: "Successful",
                                    toaster: `b-toaster-top-right`,
                                    variant: "success",
                                }
                            );
                            _this.$router.push({ name: "groups-index" });
                        })
                        .catch((errors) => {
                            if (
                                errors.response &&
                                errors.response.data.errors
                            ) {
                                _this.formErrors = errors.response.data.errors;
                            } else {
                                _this.formErrors.push(
                                    "Failed to create group. Please try again later."
                                );
                            }
                        })
                        .finally(() => {
                            _this.isLoading = false;
                        });
                }
            });
        },
        async fetchGroupData() {
            try {
                const response = await axios.get(`/groups/${this.id}`);
                this.formData = {
                    name: response.data.name,
                    status: response.data.status,
                };
            } catch (error) {
                console.error("Error fetching group data:", error);
            }
        },
    },
    mounted() {
        this.fetchGroupData();
    },
};
</script>
