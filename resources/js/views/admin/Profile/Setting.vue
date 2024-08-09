<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Change password</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form
                            class="user"
                            ref="changePasswordForm"
                            @submit.prevent="changePassword"
                        >
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Old Password</label
                                >
                                <div class="password">
                                    <input
                                        id="old_password"
                                        name="old_password"
                                        v-model="old_password"
                                        class="form-control"
                                        placeholder="Old Password"
                                        ref="old_password"
                                        :type="
                                            showOldPassword
                                                ? 'text'
                                                : 'password'
                                        "
                                        v-validate="'required|alpha_num'"
                                    />
                                    <span
                                        class="password-toggle"
                                        @click="toggleOldPassword"
                                    >
                                        <i
                                            :class="{
                                                'icon-eye-off': showOldPassword,
                                                'icon-eye': !showOldPassword,
                                            }"
                                        ></i>
                                    </span>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('old_password')"
                                    >
                                        {{ errors.first("old_password") }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Password</label>
                                <div class="password">
                                    <input
                                        id="password"
                                        name="password"
                                        v-model="password"
                                        class="form-control"
                                        placeholder="Password"
                                        ref="password"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        v-validate="
                                            'required|min:8|max:25|alpha_num'
                                        "
                                    />
                                    <span
                                        class="password-toggle"
                                        @click="togglePassword"
                                    >
                                        <i
                                            :class="{
                                                'icon-eye-off': showPassword,
                                                'icon-eye': !showPassword,
                                            }"
                                        ></i>
                                    </span>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('password')"
                                    >
                                        {{ errors.first("password") }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Confirm Password</label
                                >
                                <div class="password">
                                    <input
                                        autocomplete="off"
                                        name="password_confirmation"
                                        v-model="password_confirmation"
                                        class="form-control"
                                        placeholder="Confirm password"
                                        :type="
                                            confirmPassword
                                                ? 'text'
                                                : 'password'
                                        "
                                        v-validate="
                                            'required|confirmed:password|min:8|max:25'
                                        "
                                        data-vv-as="password"
                                    />
                                    <span
                                        class="password-toggle"
                                        @click="toggleConfirmPassword"
                                    >
                                        <i
                                            :class="{
                                                'icon-eye-off': confirmPassword,
                                                'icon-eye': !confirmPassword,
                                            }"
                                        ></i>
                                    </span>
                                </div>
                                <small
                                    class="text-danger"
                                    v-show="errors.has('password_confirmation')"
                                >
                                    {{ errors.first("password_confirmation") }}
                                </small>
                            </div>

                            <!-- Error Messages -->
                            <div
                                v-if="apiErrors.length"
                                class="alert alert-danger mt-3"
                            >
                                <ul>
                                    <li v-for="error in apiErrors" :key="error">
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-site">
                                    Reset Password
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
import * as notify from "../../../utils/notify.js";

export default {
    name: "Reset",

    data() {
        return {
            showOldPassword: false,
            showPassword: false,
            confirmPassword: false,
            isLoading: false,
            user: this.$store.state.auth.user,
            old_password: "",
            password: "",
            password_confirmation: "",
            apiErrors: [],
        };
    },
    methods: {
        toggleOldPassword() {
            this.showOldPassword = !this.showOldPassword;
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        toggleConfirmPassword() {
            this.confirmPassword = !this.confirmPassword;
        },
        async changePassword() {
            this.apiErrors = []; // Clear previous errors
            const validated = await this.$validator.validateAll();
            if (validated) {
                this.isLoading = true;
                try {
                    const response = await axios.post("/change-password", {
                        old_password: this.old_password.trim(),
                        password: this.password.trim(),
                        password_confirmation:
                            this.password_confirmation.trim(),
                    });

                    this.$toasted.show("Password updated successfully", {
                        theme: "toasted-primary",
                        position: "top-right",
                        duration: 5000,
                    });
                } catch (error) {
                    // Capture API error messages
                    if (error.response && error.response.data.errors) {
                        for (const key in error.response.data.errors) {
                            this.apiErrors.push(
                                ...error.response.data.errors[key]
                            );
                        }
                    } else {
                        this.apiErrors.push(
                            error.response?.data?.message || "An error occurred"
                        );
                    }
                } finally {
                    this.isLoading = false;
                }
            }
        },
    },
};
</script>
