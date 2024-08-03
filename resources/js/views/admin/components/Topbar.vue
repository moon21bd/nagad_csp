<template>
    <div
        class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"
    >
        <!-- Sidebar Toggle (Topbar) -->
        <button
            id="sidebarToggleTop"
            class="btn btn-link d-md-none rounded-circle mr-3"
        >
            <i class="icon-left"></i>
        </button>
        <button class="btn push-menu" id="sidebarToggle">
            <i class="icon-left"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">
            <NotificationDropdown />

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown nav-user no-arrow">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="userDropdown"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <img
                        class="img-profile rounded-circle"
                        src="/images/user-avatar.png"
                    />
                    <span class="ml-2 d-none d-lg-inline text-gray-600 small">
                        <span v-if="user">{{ user.name }}</span>
                        <i class="icon-down"></i>
                    </span>
                </a>
                <!-- Dropdown - User Information -->
                <div
                    class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown"
                >
                    <a class="dropdown-item" href="/admin/profile">
                        <i class="icon-user mr-2 text-gray-600"></i>
                        Profile
                    </a>

                    <div class="dropdown-divider"></div>
                    <a
                        class="dropdown-item"
                        href="javascript:void(0)"
                        @click="logout"
                        data-toggle="modal"
                        data-target="#logoutModal"
                    >
                        <i class="icon-logout mr-2 text-gray-600"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import NotificationDropdown from "./NotificationDropdown.vue";

export default {
    name: "Topbar",
    components: {
        NotificationDropdown,
    },
    computed: {
        ...mapState("auth", ["user"]),
        ...mapGetters("auth", ["user"]),
        user() {
            return this.$store.getters["auth/user"];
        },
    },
    methods: {
        ...mapActions("auth", ["setUser"]),
        logout() {
            this.logoutSend(this.$store.state.auth.user.id);
            this.$store.dispatch("auth/logout");
            this.$router.push("/login");
        },
        async logoutSend(id) {
            console.log("logoutSend Called", id);
            await axios
                .post("/logout", { id })
                .then(({ data }) => {
                    console.log("logout-message", data);
                })
                .catch(({ response: { data } }) => {
                    console.log("logout-data", data);
                    this.errors = data.errors;
                })
                .finally(() => {
                    console.log("Logged Out!");
                });
        },
    },
};
</script>
