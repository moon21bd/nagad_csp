<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Reset password</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form
                            class="user"
                            ref="resetPasswordForm"
                            @submit.prevent="resetPassword"
                        >
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Password<sup>*</sup></label
                                    >
                                    <div class="password">
                                        <input
                                            id="password"
                                            name="password"
                                            v-model="password"
                                            class="form-control"
                                            placeholder="Ex: Ab@!M345"
                                            ref="password"
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            v-validate="
                                                'required|min:8|max:25|password_format|strong_password'
                                            "
                                            @input="checkPasswordStrength"
                                        />
                                        <span
                                            class="password-toggle"
                                            @click="togglePassword"
                                        >
                                            <i :class="passwordIcon"></i>
                                        </span>
                                        <!-- <span
                                        class="password-toggle"
                                        @click="togglePassword"
                                    >
                                        <i
                                            :class="{
                                                'icon-eye-off': showPassword,
                                                'icon-eye': !showPassword,
                                            }"
                                        ></i>
                                    </span> -->
                                    </div>
                                    <small
                                        class="d-block mt-1"
                                        v-if="this.password"
                                    >
                                        Strength:
                                        <span :class="passwordStrengthClass">{{
                                            passwordStrengthText
                                        }}</span>
                                    </small>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('password')"
                                    >
                                        {{ errors.first("password") }}
                                    </small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Confirm Password<sup>*</sup></label
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
                                                    'icon-eye-off':
                                                        confirmPassword,
                                                    'icon-eye':
                                                        !confirmPassword,
                                                }"
                                            ></i>
                                        </span>
                                    </div>
                                    <small
                                        class="text-danger"
                                        v-show="
                                            errors.has('password_confirmation')
                                        "
                                    >
                                        {{
                                            errors.first(
                                                "password_confirmation"
                                            )
                                        }}
                                    </small>
                                </div>
                            </div>
                            <!-- Error Messages -->
                            <div class="col-md-6 form-group">
                                <div
                                    v-if="apiErrors.length"
                                    class="alert alert-danger mt-3"
                                >
                                    <ul>
                                        <li
                                            v-for="error in apiErrors"
                                            :key="error"
                                        >
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>
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
import axios from "../../../axios.js";
import * as notify from "../../../utils/notify.js";

import zxcvbn from "zxcvbn";
import { Validator } from "vee-validate";

Validator.extend("password_format", {
    getMessage: (field) =>
        `${field} must include at least one uppercase letter, one lowercase letter, one number, and one special character.`,
    validate: (value) => {
        const regex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,25}$/;
        console.log("Regex validation result:", regex.test(value)); // Add logging
        return regex.test(value);
    },
});

Validator.extend("strong_password", {
    getMessage: (field) => `${field} must be strong enough.`,
    validate: (value) => {
        const result = zxcvbn(value);
        return result.score >= 2; // Require at least "Fair" password strength (score 2 or higher)
    },
});

export default {
    name: "Reset",

    data() {
        return {
            passwordStrength: 0,
            id: this.$route.params.id,
            showOldPassword: false,
            showPassword: false,
            confirmPassword: false,
            isLoading: false,
            user: this.$store.state.auth.user,
            password: "",
            password_confirmation: "",
            apiErrors: [],
        };
    },
    computed: {
        passwordIcon() {
            return this.showPassword ? "icon-eye-off" : "icon-eye";
        },
        passwordStrengthText() {
            const strengthLevels = [
                "Very Weak",
                "Weak",
                "Fair",
                "Strong",
                "Very Strong",
            ];
            return strengthLevels[this.passwordStrength];
        },
        passwordStrengthClass() {
            switch (this.passwordStrengthText) {
                case "Very Weak":
                case "Weak":
                    return "text-danger";
                case "Fair":
                    return "text-info";
                case "Strong":
                case "Very Strong":
                    return "text-success";
                default:
                    return "text-dark";
            }
        },
    },
    methods: {
        checkPasswordStrength() {
            if (!this.password || this.password.length === 0) {
                return;
            }
            const result = zxcvbn(this.password);
            this.passwordStrength = result.score;

            // Trigger validation
            this.$validator.validate("password");
        },
        toggleOldPassword() {
            this.showOldPassword = !this.showOldPassword;
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        toggleConfirmPassword() {
            this.confirmPassword = !this.confirmPassword;
        },
        async resetPassword() {
            const _this = this;
            this.apiErrors = []; // Clear previous errors
            const validated = await _this.$validator.validateAll();
            console.log("this.id", _this.id);
            if (validated && this.passwordStrength >= 2) {
                _this.isLoading = true;
                try {
                    const response = await axios.put(
                        `/user/${this.id}/reset-password`,
                        {
                            password: this.password.trim(),
                            password_confirmation:
                                this.password_confirmation.trim(),
                        }
                    );

                    // Check if the response status is OK (200)
                    if (response.status === 200) {
                        // Reset form fields
                        this.password = "";
                        this.password_confirmation = "";
                        // Reset validation errors
                        this.apiErrors = [];

                        this.$refs.resetPasswordForm.reset();
                        this.$router.push({ name: "user-index" });

                        this.$showToast("Password reset successfully", {
                            type: "success",
                        });
                    } else {
                        // Handle unexpected response status if needed
                        this.apiErrors.push(
                            response.data?.message ||
                                "An unexpected error occurred"
                        );
                    }
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
            } else if (this.passwordStrength < 2) {
                // Show error if password is too weak
                this.formErrors.push(
                    "Password strength is too weak. Please choose a stronger password."
                );
            }
        },
    },
};
</script>
