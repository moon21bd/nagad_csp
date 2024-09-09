<template>
    <div class="login-container vh-100 align-items-center">
        <div class="login-content text-center text-white">
            <h2>Welcome to</h2>
            <img src="/images/logo-white.svg" alt="" />
            <p>
                With the love of crores of people Nagad won Most Emerging Brand
                of Bangladesh {{ new Date().getFullYear() }}
            </p>
            <div class="design">Design by <a href="#">DIGICON</a></div>
        </div>
        <div class="login-box d-flex vh-100 align-items-center">
            <div class="login-box-inner">
                <div v-if="!emailSent">
                    <h1 class="my-4">Forgot Password?</h1>
                    <form class="user" @submit.prevent="forgot">
                        <div class="form-group">
                            <input
                                type="email"
                                class="form-control form-control-user"
                                aria-describedby="emailHelp"
                                placeholder="Enter Email Address..."
                                v-model="email"
                            />
                        </div>
                        <LoadingButton
                            text="Reset password"
                            v-bind:isLoading="isLoading"
                        />
                    </form>
                </div>
                <div v-else>
                    <span class="h4">
                        <i class="icon-check"></i>
                        Check your email!
                    </span>
                </div>

                <div class="text-center">
                    <router-link class="small" :to="{ name: 'login' }"
                        >Already have an account? Login!</router-link
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingButton from "../../components/LoadingButton";
import * as notify from "../../utils/notify.js";

export default {
    name: "Forgot",
    components: {
        LoadingButton,
    },
    data() {
        return {
            email: this.email,
            isLoading: false,
            emailSent: false,
        };
    },
    methods: {
        async forgot() {
            this.isLoading = true;
            try {
                await axios.post("forgot", {
                    email: this.email,
                });
                this.isLoading = false;
                this.emailSent = true;
            } catch (error) {
                notify.authError(error);
                this.isLoading = false;
            }
        },
    },
};
</script>
