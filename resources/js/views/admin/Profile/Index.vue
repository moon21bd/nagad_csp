<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">{{ user.name }}</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="custom-list">
                    <ul>
                        <li><strong>Name</strong> {{ user.name }}</li>

                        <li><strong>Email</strong> {{ user.email }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "profile",
    data() {
        return {
            isLoading: false,
            user: this.$store.state.auth.user,
        };
    },
};
</script>
