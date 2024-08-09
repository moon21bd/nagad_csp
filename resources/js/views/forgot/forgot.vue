<template>
    <div class="login-container align-items-center">
        <div class="login-img vh-100 align-self-start">
            <img
                class="img-fluid vh-100"
                src="/images/login-bg-sm.png"
                alt=""
            />
            <!-- <img
              class="img-fluid vh-100"
              src="https://images.unsplash.com/photo-1719776049588-e1997c9066dd?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
              alt=""
          /> -->
        </div>
        <div class="login-box">
            <div class="d-flex justify-content-center">
                <div class="col-md-7 col-xl-7">
                    <img src="/images/logo.svg" alt="" />

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
