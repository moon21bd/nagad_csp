<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'dnd-user-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Update DnD User</h1>
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
                                        v-model="dnduser.name"
                                    />
                                    <span
                                        v-if="errors.name"
                                        class="text-danger"
                                        >{{ errors.name[0] }}</span
                                    >

                                    <div
                                        class="text-danger"
                                        v-show="errors.has('name')"
                                    >
                                        {{ errors.first("name") }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Phone Number<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="mobile_no"
                                        v-model="dnduser.mobile_no"
                                        v-validate="
                                            'required|numeric|min:10|max:14'
                                        "
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('mobile_no')"
                                    >
                                        {{ errors.first("mobile_no") }}
                                    </div>
                                </div>
                            </div>

                            <small
                                v-if="formErrors.message"
                                class="text-danger"
                                >{{ formErrors.message }}</small
                            >
                            <br />
                            <br />
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
import axios from "../../../axios";

export default {
    data() {
        return {
            isLoading: false,
            formErrors: [],
            id: null,
            dnduser: {
                name: "",
                mobile_no: "",
            },
        };
    },
    created() {
        this.id = this.$route.params.id;
        this.fetchDnDUser();
    },
    methods: {
        async fetchDnDUser() {
            try {
                const response = await axios.get(`/dnd-user/${this.id}`);
                this.dnduser = {
                    name: response.data.name,
                    mobile_no: response.data.mobile_no,
                };
            } catch (error) {
                console.error("Error fetching dnd user:", error);
                this.dnduser = {};
            }
        },
        async handleSubmit() {
            const _this = this;
            _this.$validator.validateAll().then(async (result) => {
                if (result) {
                    axios({
                        method: "PUT",
                        url: `/dnd-user/${_this.id}`,
                        data: _this.dnduser,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            _this.isLoading = false;
                            _this.dnduser = {};
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
                                    "Failed to create user. Please try again later."
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
