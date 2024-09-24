<template>
    <div class="login-container vh-100 align-items-center">
        <div class="login-content text-center text-white">
            <h2>Welcome to</h2>
            <img src="/images/logo-white.svg" alt="" />
            <p>
                With the love of crores of people Nagad won Most Emerging Brand
                of Bangladesh {{ new Date().getFullYear() }}
            </p>
            <div class="design">Developed by Digicon Technologies PLC</a></div>
        </div>
        <div class="login-box d-flex vh-100 align-items-center">
            <div class="login-box-inner">
                <h1 class="my-4">Login</h1>
                <label class="control-label"
                    >Please fill in your credentials to login.</label
                >
                <div
                    v-if="verificationStatus"
                    class="alert alert-dismissible fade show mt-5"
                    :class="verificationAlertClasses"
                    role="alert"
                >
                    <div>{{ verificationMessage }}</div>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="user" @submit.prevent="login">
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control form-control-user"
                            id="exampleInputEmail"
                            aria-describedby="emailHelp"
                            placeholder="Enter Email or Employee User ID"
                            v-model="email"
                            name="emailOrEmployeeId"
                            v-validate="'required'"
                        />
                        <small
                            class="text-danger"
                            v-show="
                                errors.has(
                                    'emailOrEmployeeId'
                                )
                            "
                        >
                            {{
                                errors.first(
                                    "emailOrEmployeeId"
                                )
                            }}
                        </small>
                    </div>
                    <div class="form-group">
                        <div class="password">
                            <input
                                type="password"
                                class="form-control form-control-user"
                                id="exampleInputPassword"
                                placeholder="Password"
                                name="password"
                                v-model="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-validate="'required|min:8|max:25'"
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
                        </div>
                         <small
                            class="text-danger"
                            v-show="
                                errors.has(
                                    'password'
                                )
                            "
                        >
                            {{
                                errors.first(
                                    "password"
                                )
                            }}
                        </small>
                    </div>
                    <div class="form-group">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <label class="checkbox"
                                ><input type="checkbox" id="customCheck" /><span
                                    class="checkmark"
                                ></span
                                >Remember Me</label
                            >
                            <router-link class="small" :to="{ name: 'forgot' }"
                                >Forgot Password?
                            </router-link>
                        </div>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import * as notify from "../../utils/notify.js";
export default {
    name: "Login",
    data() {
        return {
            showPassword: false,
            email: "",
            password: "",
            verificationStatus: this.$route.query.verification_status
                ? true
                : false,
            verificationMessage: "",
            verificationAlertClasses: {
                "alert-success": false,
                "alert-danger": false,
            },
        };
    },
    created: function () {
        if (this.$route.query.verification_status === "success") {
            this.verificationMessage =
                "Your account has been verified. Please log in.";
            this.verificationAlertClasses["alert-success"] = true;
        } else if (this.$route.query.verification_status === "error") {
            this.verificationMessage = "Your account could not be verified.";
            this.verificationAlertClasses["alert-danger"] = true;
        }
    },

    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        async login() {

            const validated = await this.$validator.validateAll();

            if (validated) {

                try {
                    const response = await axios.post("login", {
                        email: this.email,
                        password: this.password,
                    });

                    const user = response.data.user;

                    if (response.data.requiresLocation) {
                        try {
                            const location = await this.getLocation();
                            const locationResponse = await axios.post(
                                "complete-login",
                                {
                                    userId: user.id,
                                    location,
                                }
                            );

                            const token = locationResponse.data.token;
                            console.log("token", token);
                            localStorage.setItem("token", token);

                            this.$store.dispatch(
                                "auth/setUser",
                                locationResponse.data.user
                            );
                            this.$store.commit("auth/SET_TOKEN", token);

                            axios.defaults.headers.common[
                                "Authorization"
                            ] = `Bearer ${token}`;
                            this.$router.push("/admin");
                        } catch (locationError) {
                            // console.error("Error getting location:", locationError);
                            if (locationError.code === 1) {
                                this.notifyAuthError(
                                    "Location access denied. Please enable location services to proceed."
                                );
                            } else {
                                this.notifyAuthError(
                                    "Error getting location. Please try again."
                                );
                            }
                        }
                    } else if (response.data.requiresFirstPasswordChange) {
                        const { data } = response;

                        this.notifyAuthError(
                            data.message
                        );

                        this.$router.push({ name: 'change-password', params: { token:data.token } });
                        
                    } else {
                        const token = response.data.token;
                        console.log("token", token);
                        localStorage.setItem("token", token);

                        this.$store.dispatch("auth/setUser", response.data.user);
                        this.$store.commit("auth/SET_TOKEN", token);

                        axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${token}`;
                        this.$router.push("/admin");
                    }
                } catch (error) {
                    // Check if error response exists and has a message or validation errors
                    if (error.response) {
                        const { data } = error.response;

                        if (data.errors && data.errors.email) {
                            // Specific handling for validation errors
                            this.notifyAuthError(data.errors.email[0]);
                        } else if (data.message) {
                            // General error message from the server
                            this.notifyAuthError(data.message);
                        } else {
                            // Fallback for unexpected error formats
                            this.notifyAuthError("Login failed. Please try again.");
                        }
                    } else {
                        // Handle errors with no response
                        this.notifyAuthError(
                            "Network error. Please check your connection."
                        );
                    }
                }
            }
            
        },
        getLocation() {
            return new Promise((resolve, reject) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            resolve({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                            });
                        },
                        (error) => {
                            reject(error);
                        }
                    );
                } else {
                    reject(
                        new Error(
                            "Geolocation is not supported by this browser."
                        )
                    );
                }
            });
        },
        notifyAuthError(message) {
            this.$showToast(message, {
                title: "Error",
                type: "warning",
            });
        },
    },
};
</script>

<style>
.checkmark {
    border: 2px solid #f56530;
}
.checkbox input:checked ~ .checkmark {
    background: #f56530;
    border: 2px solid #f56530;
}
</style>
