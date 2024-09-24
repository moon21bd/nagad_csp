<template>
    <div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div
                                    class="col-lg-6 d-none d-lg-block bg-password-image"
                                ></div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-2">
                                                Change your password
                                            </h1>
                                        </div>
                                        <form
                                            class="user"
                                            @submit.prevent="changePassword"
                                            ref="changePasswordForm"
                                        >
                                            <div class="form-group">
                                                <input
                                                    type="password"
                                                    class="form-control form-control-user"
                                                    placeholder="Ex: Ab@!M345"
                                                    v-model="new_password"
                                                    name="new_password"
                                                    ref="password"
                                                    autocomplete="new-password"
                                                    :type="
                                                        showPassword
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    v-validate="
                                                        'required|min:8|max:25|password_format|strong_password'
                                                    "
                                                    @input="
                                                        checkPasswordStrength
                                                    "
                                                />
                                                <span
                                                    class="password-toggle"
                                                    @click="togglePassword"
                                                >
                                                    <i
                                                        :class="passwordIcon"
                                                    ></i>
                                                </span>

                                                <small
                                                    class="d-block mt-1"
                                                    v-if="this.new_password"
                                                >
                                                    Strength:
                                                    <span
                                                        :class="
                                                            passwordStrengthClass
                                                        "
                                                        >{{
                                                            passwordStrengthText
                                                        }}</span
                                                    >
                                                </small>

                                                <small
                                                    class="text-danger"
                                                    v-show="
                                                        errors.has(
                                                            'new_password'
                                                        )
                                                    "
                                                >
                                                    {{
                                                        errors.first(
                                                            "new_password"
                                                        )
                                                    }}
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <input
                                                    type="password"
                                                    class="form-control form-control-user"
                                                    v-model="
                                                        password_confirmation
                                                    "
                                                    name="password_confirmation"
                                                    placeholder="Confirm password"
                                                    :type="
                                                        confirmPassword
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    autocomplete="new-password"
                                                    v-validate="
                                                        'required|confirmed:password|min:8|max:25'
                                                    "
                                                    data-vv-as="password"
                                                />
                                                <span
                                                    class="password-toggle"
                                                    @click="
                                                        toggleConfirmPassword
                                                    "
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
                                                <small
                                                    class="text-danger"
                                                    v-show="
                                                        errors.has(
                                                            'password_confirmation'
                                                        )
                                                    "
                                                >
                                                    {{
                                                        errors.first(
                                                            "password_confirmation"
                                                        )
                                                    }}
                                                </small>
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
                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-user btn-block"
                                            >
                                                Change Password
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";
import * as notify from "../../utils/notify.js";
import zxcvbn from "zxcvbn";
import { Validator } from "vee-validate";

Validator.extend("password_format", {
    getMessage: (field) =>
        `${field} must include at least one uppercase letter, one lowercase letter, one number, and one special character.`,
    validate: (value) => {
        const regex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,25}$/;
        // console.log("Regex validation result:", regex.test(value)); // Add logging
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
    name: "ChangeFirstPassword",
    data() {
        return {
            passwordStrength: 0,
            showPassword: false,
            confirmPassword: false,
            password_confirmation: "",
            apiErrors: [],
            new_password: "",
            token: "",
        };
    },
    created() {
        this.token = this.$route.params.token || "";
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
            if (!this.new_password || this.new_password.length === 0) {
                return;
            }

            const result = zxcvbn(this.new_password);
            this.passwordStrength = result.score;

            // Trigger validation
            this.$validator.validate("new_password");
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

            if (validated && this.passwordStrength >= 2) {
                try {
                    const response = await axios.post(
                        "/change-password-first-time",
                        {
                            new_password: this.new_password.trim(),
                            new_password_confirmation:
                                this.password_confirmation.trim(),
                            token: this.token,
                        }
                    );

                    // Check if the response status is OK (200)
                    if (response.status === 200) {
                        // Reset form fields
                        this.new_password = "";
                        this.password_confirmation = "";
                        this.apiErrors = [];

                        this.$refs.changePasswordForm.reset();
                        this.$showToast(response.data.message, {
                            type: "success",
                        });
                        this.$router.push("/login");
                    } else {
                        // Handle unexpected response status if needed

                        this.$refs.changePasswordForm.reset();
                        this.$showToast(
                            response.data.message ||
                                "An unexpected error occurred",
                            {
                                type: "error",
                            }
                        );
                    }
                } catch (error) {
                    // Capture API error messages
                    this.$refs.changePasswordForm.reset();

                    if (error.response && error.response.data.errors) {
                        for (const key in error.response.data.errors) {
                            this.apiErrors.push(
                                ...error.response.data.errors[key]
                            );
                        }
                    } else {
                        this.$showToast(
                            error.response?.data?.message ||
                                "An error occurred",
                            {
                                type: "error",
                            }
                        );
                    }
                }
            }
        },
    },
};
</script>
