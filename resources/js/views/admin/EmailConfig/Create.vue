<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'email-config-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create EMail Config</h1>
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
                                <label class="control-label"
                                    >Mailer<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.mailer"
                                    name="mailer"
                                    placeholder="smtp"
                                    v-validate="'required'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('mailer')"
                                >
                                    {{ errors.first("mailer") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Host<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.host"
                                    name="host"
                                    placeholder="smtp.office365.com"
                                    v-validate="'required'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('host')"
                                >
                                    {{ errors.first("host") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Port<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.port"
                                    name="port"
                                    placeholder="587"
                                    v-validate="'required'"
                                    type="number"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('port')"
                                >
                                    {{ errors.first("port") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Username<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.username"
                                    name="username"
                                    placeholder="noreply@nagad.com.bd"
                                    v-validate="'required|email'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('username')"
                                >
                                    {{ errors.first("username") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Password<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.password"
                                    name="password"
                                    placeholder="Password"
                                    v-validate="'required'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('password')"
                                >
                                    {{ errors.first("password") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Encryption<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.encryption"
                                    name="encryption"
                                    placeholder="tls"
                                    v-validate="'required'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('encryption')"
                                >
                                    {{ errors.first("encryption") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >From Address<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.from_address"
                                    name="from_address"
                                    placeholder="noreply@nagad.com.bd"
                                    v-validate="'required|email'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('from_address')"
                                >
                                    {{ errors.first("from_address") }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >From Name<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="formData.from_name"
                                    name="from_name"
                                    placeholder="Nagad Commercial"
                                    v-validate="'required'"
                                    type="text"
                                />
                                <div
                                    class="text-danger"
                                    v-show="errors.has('from_name')"
                                >
                                    {{ errors.first("from_name") }}
                                </div>
                            </div>
                            <br />
                            <div>
                                <ul class="list-group" v-if="formErrors.length">
                                    <li
                                        class="list-group-item list-group-item-danger"
                                        v-for="error in formErrors"
                                        :key="error.field"
                                    >
                                        <strong>{{ error.field }}:</strong>
                                        <ul>
                                            <li
                                                v-for="message in error.messages"
                                                :key="message"
                                            >
                                                {{ message }}
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <br />

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
                mailer: "",
                host: "",
                port: "",
                username: "",
                password: "",
                encryption: "",
                from_address: "",
                from_name: "",
                status: "active",
            },
            formErrors: [],
        };
    },
    methods: {
        async handleSubmit() {
            const _this = this;
            _this.isLoading = true; // Start loading

            _this.$validator.validateAll().then(async (result) => {
                if (result) {
                    axios({
                        method: "POST",
                        url: "/email-config",
                        data: _this.formData,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            // Check if the response contains validation errors
                            if (response.data.errors) {
                                _this.handleErrors(response.data.errors);
                            } else {
                                // On success, show success message and redirect
                                Vue.prototype.$showToast(
                                    "Email Config created successfully.",
                                    {
                                        type: "success",
                                    }
                                );
                                _this.$router.push({
                                    name: "email-config-index",
                                });
                            }
                        })
                        .catch((error) => {
                            // Handle errors from the catch block
                            this.formErrors = []; // Clear previous errors

                            if (error.response && error.response.data.errors) {
                                // Extract and display validation errors
                                const errors = error.response.data.errors;
                                for (const [key, messages] of Object.entries(
                                    errors
                                )) {
                                    // Assuming the formErrors array should be an array of objects with field and message keys
                                    this.formErrors.push({
                                        field: key,
                                        messages,
                                    });
                                }
                            } else {
                                // Generic error message
                                this.formErrors.push({
                                    field: null,
                                    messages: [
                                        "Failed to update email config. Please try again later.",
                                    ],
                                });
                            }
                            /* if (error.response && error.response.data.errors) {
                                _this.handleErrors(error.response.data.errors);
                            } else {
                                // Show a generic error message
                                Vue.prototype.$showToast(
                                    "Failed to create Email Config. Please try again later.",
                                    {
                                        type: "error",
                                    }
                                );
                            } */
                        })
                        .finally(() => {
                            _this.isLoading = false; // End loading
                        });
                }
            });
        },

        handleErrors(errors) {
            // Loop through errors and display each one using a toast
            for (const [field, messages] of Object.entries(errors)) {
                messages.forEach((message) => {
                    Vue.prototype.$showToast(message, {
                        type: "error",
                    });
                });
            }
        },
    },
};
</script>
