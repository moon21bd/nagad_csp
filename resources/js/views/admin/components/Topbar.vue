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
        <button class="btn push-menu d-none d-sm-flex" id="sidebarToggle">
            <i class="icon-left m-auto"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto align-items-center">
            <li>
                <div class="switch mr-3">
                    <label class="switch-label">
                        <input
                            type="radio"
                            value="Active"
                            v-model="currentStatus"
                            @change="changeStatus('Active')"
                        />
                        <span>Active</span>
                    </label>

                    <label class="switch-label">
                        <input
                            type="radio"
                            value="Break"
                            v-model="currentStatus"
                            @change="changeStatus('Break')"
                        />
                        <span>Break</span>
                    </label>

                    <div
                        class="slider"
                        :class="{ break: currentStatus === 'Break' }"
                    ></div>
                </div>
                <!-- <p>Current Status: {{ currentStatus }}</p> -->
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item">
                <a href="" class="nav-link"
                    ><button class="btn btn-refresh">
                        <i class="icon-refresh"></i></button
                ></a>
            </li>

            <!-- Status Update Radio Buttons -->
            <!-- <li class="nav-item">
                <div class="btn-group" role="group" aria-label="Status Update">
                    <label>
                        <input
                            type="radio"
                            value="Active"
                            v-model="status"
                            @change="updateStatus"
                        />
                        Active
                    </label>
                    <label>
                        <input
                            type="radio"
                            value="Break"
                            v-model="status"
                            @change="updateStatus"
                        />
                        Break
                    </label>
                </div>
            </li> -->

            <div class="topbar-divider d-none d-sm-block"></div>
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
                    <div class="position-relative">
                        <img
                            class="img-profile rounded-circle"
                            src="/images/user-avatar.png"
                        />

                        <span
                            class="badge-status position-absolute"
                            :style="{
                                background:
                                    currentStatus === 'Break'
                                        ? '#f39c12'
                                        : '#1abc9c',
                            }"
                        ></span>
                    </div>
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
                    <router-link
                        class="dropdown-item"
                        :to="{ name: 'user-profile' }"
                    >
                        <i class="icon-user mr-2 text-gray-600"></i>
                        Profile
                    </router-link>

                    <router-link
                        class="dropdown-item"
                        :to="{ name: 'user-change-password' }"
                    >
                        <i class="icon-lock mr-2 text-gray-600"></i>
                        Change Password
                    </router-link>

                    <div class="dropdown-divider"></div>
                    <router-link
                        class="dropdown-item"
                        to="#"
                        @click.native.prevent="logout"
                        data-toggle="modal"
                        data-target="#logoutModal"
                    >
                        <i class="icon-logout mr-2 text-gray-600"></i>
                        Logout
                    </router-link>
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
        ...mapGetters("sessionStatus", ["currentStatus"]),

        user() {
            return this.$store.getters["auth/user"];
        },
    },
    mounted() {
        this.loadCurrentStatus();
    },
    methods: {
        ...mapActions("auth", ["setUser"]),
        ...mapActions("sessionStatus", ["updateStatus", "fetchCurrentStatus"]),
        logout() {
            this.logoutSend(this.$store.state.auth.user.id);
            this.$store.dispatch("auth/logout");
            this.$router.push("/login");
        },
        changeStatus(newStatus) {
            this.updateStatus(newStatus);
        },
        loadCurrentStatus() {
            this.fetchCurrentStatus();
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
