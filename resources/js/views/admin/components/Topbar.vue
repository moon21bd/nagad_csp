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
            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow nav-notify mx-1">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="alertsDropdown"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i class="icon-bell"></i>
                    <!-- Counter - Alerts -->
                    <span class="badge badge-counter">3+</span>
                </a>
                <!-- Dropdown - Alerts -->
                <div
                    class="d-none dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown"
                >
                    <h6 class="dropdown-header">Alerts Center</h6>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">
                                December 12, 2019
                            </div>
                            <span class="font-weight-bold"
                                >A new monthly report is ready to
                                download!</span
                            >
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-donate text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">
                                December 7, 2019
                            </div>
                            $290.29 has been deposited into your account!
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i
                                    class="fas fa-exclamation-triangle text-white"
                                ></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">
                                December 2, 2019
                            </div>
                            Spending Alert: We've noticed unusually high
                            spending for your account.
                        </div>
                    </a>
                    <a
                        class="dropdown-item text-center small text-gray-500"
                        href="#"
                        >Show All Alerts</a
                    >
                </div>
            </li>

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
                    <span class="ml-2 d-none d-lg-inline text-gray-600 small"
                        >{{ user.first_name }} {{ user.last_name }}
                        <i class="icon-down"></i
                    ></span>
                </a>
                <!-- Dropdown - User Information -->
                <div
                    class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown"
                >
                    <a class="dropdown-item" href="#">
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
import { mapGetters } from "vuex";

export default {
    name: "Topbar",
    computed: {
        ...mapGetters(["user"]),
    },
    methods: {
        logout() {
            localStorage.removeItem("token");
            this.$store.dispatch("user", null);
            this.$router.push("/login");
        },
    },
};
</script>
