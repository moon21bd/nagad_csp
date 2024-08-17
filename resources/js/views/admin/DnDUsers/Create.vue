<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'dnd-user-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create DnD User</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >User Name</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="name"
                                        placeholder="Name"
                                        v-model="formData.name"
                                    />
                                    <span
                                        v-if="errors.name"
                                        class="text-danger"
                                        >{{ errors.name[0] }}</span
                                    >

                                    <small
                                        class="text-danger"
                                        v-show="errors.has('name')"
                                    >
                                        {{ errors.first("name") }}
                                    </small>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Phone Number<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="mobile_no"
                                        placeholder="Mobile No."
                                        v-model="formData.mobile_no"
                                        v-validate="
                                            'required|numeric|min:10|max:14'
                                        "
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('mobile_no')"
                                    >
                                        {{ errors.first("mobile_no") }}
                                    </small>
                                </div>
                            </div>

                            <div v-if="formErrors.message" class="mb-3">
                                <small class="text-danger">{{
                                    formErrors.message
                                }}</small>
                            </div>

                            <div>
                                <button class="btn btn-site" type="submit">
                                    Create
                                </button>
                            </div>
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
            formErrors: [],
            formData: {
                name: "",
                mobile_no: "",
            },
        };
    },
    methods: {
        async handleSubmit() {
            const _this = this;
            _this.$validator.validateAll().then(async (validated) => {
                if (validated) {
                    axios({
                        method: "POST",
                        url: "/dnd-user",
                        data: _this.formData,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            _this.isLoading = false;
                            _this.formData = {};
                            Vue.prototype.$showToast(response.data.message, {
                                type: "success",
                            });
                            _this.$router.push({ name: "dnd-user-index" });
                        })
                        .catch((errors) => {
                            console.log(
                                "err.response",
                                errors.response.data.errors
                            );
                            if (
                                errors.response &&
                                errors.response.data.errors
                            ) {
                                _this.formErrors = errors.response.data.errors;
                            } else {
                                _this.formErrors.push(
                                    "Failed to create dnd user. Please try again later."
                                );
                            }
                        })
                        .finally(() => {
                            _this.isLoading = false;
                        });
                }
            });
        },
    },
};
</script>
