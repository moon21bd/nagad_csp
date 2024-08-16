<template>
    <div class="login-container align-items-center">
        <div class="login-img vh-100 align-self-start">
            <!-- <img
                class="img-fluid vh-100"
                src="/images/login-bg-sm.png"
                alt=""
            /> -->
            <img
                class="img-fluid vh-100"
                src="https://images.unsplash.com/photo-1719776049588-e1997c9066dd?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt=""
            />
        </div>
        <div class="login-box">
            <div class="d-flex justify-content-center">
                <div class="col-md-7 col-xl-7">
                    <!--                    <img src="/images/logo.svg" alt="" />-->
                    <h1 class="my-4">Login</h1>
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
                                type="email"
                                class="form-control form-control-user"
                                id="exampleInputEmail"
                                aria-describedby="emailHelp"
                                placeholder="Enter Email Address..."
                                v-model="email"
                            />
                        </div>
                        <div class="form-group">
                            <input
                                type="password"
                                class="form-control form-control-user"
                                id="exampleInputPassword"
                                placeholder="Password"
                                v-model="password"
                            />
                        </div>
                        <div class="form-group">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <label class="checkbox"
                                    ><input
                                        type="checkbox"
                                        id="customCheck"
                                    /><span class="checkmark"></span>Remember
                                    Me</label
                                >
                                <router-link
                                    class="small"
                                    :to="{ name: 'forgot' }"
                                    >Forgot Password?
                                </router-link>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-block">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";

export default {
    name: "Login",
    components: {},
    data() {
        return {
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
        async login() {
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
                // console.error("Login error:", error);
                this.notifyAuthError(
                    error.response && error.response.data
                        ? error.response.data.message
                        : "Login failed. Please try again."
                );
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

<style></style>
