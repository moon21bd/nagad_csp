<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'email-config-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Email Config</h1>
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
                                <label class="control-label">Mailer</label>
                                <input
                                    class="form-control"
                                    v-model="formData.mailer"
                                    name="mailer"
                                    type="text"
                                    v-validate="'required'"
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
            id: this.$route.params.id,
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
            this.isLoading = true; // Start loading

            this.$validator.validateAll().then(async (result) => {
                if (result) {
                    try {
                        const response = await axios({
                            method: "PUT",
                            url: `/email-config/${this.id}`,
                            data: this.formData,
                            headers: { "Content-Type": "application/json" },
                        });

                        // On success, show success message and redirect
                        Vue.prototype.$showToast(
                            "Email config updated successfully.",
                            {
                                type: "success",
                            }
                        );
                        this.$router.push({ name: "email-config-index" });
                    } catch (error) {
                        this.formErrors = []; // Clear previous errors

                        if (error.response && error.response.data.errors) {
                            // Extract and display validation errors
                            const errors = error.response.data.errors;
                            for (const [key, messages] of Object.entries(
                                errors
                            )) {
                                // Assuming the formErrors array should be an array of objects with field and message keys
                                this.formErrors.push({ field: key, messages });
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
                    } finally {
                        this.isLoading = false; // End loading
                    }
                }
            });
        },
        async fetchEmailConfigData() {
            try {
                const response = await axios.get(`/email-config/${this.id}`);
                this.formData = {
                    mailer: response.data.mailer,
                    status: response.data.status,
                    host: response.data.host,
                    port: response.data.port,
                    username: response.data.username,
                    password: response.data.password,
                    encryption: response.data.encryption,
                    from_address: response.data.from_address,
                    from_name: response.data.from_name,
                };
            } catch (error) {
                console.error("Error fetching email config data:", error);
            }
        },
    },
    mounted() {
        this.fetchEmailConfigData();
    },
};
</script>
