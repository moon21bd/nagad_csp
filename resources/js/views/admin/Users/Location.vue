<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
            >
                <i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">User Location</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <form @submit.prevent="searchUsers" class="verify-user">
                            <input
                                class="form-control"
                                type="text"
                                v-model="searchQuery"
                                name="search"
                                placeholder="Search by name or ID"
                            />
                        </form>
                        <div class="user-search">
                            <ul>
                                <li
                                    v-for="user in filteredUsers"
                                    :key="user.userId"
                                    @click="selectUser(user)"
                                >
                                    <div class="user-search-img">
                                        <img
                                            :src="
                                                user.avatar ||
                                                '/images/user-avatar.png'
                                            "
                                            alt=""
                                        />
                                    </div>
                                    <div class="user-search-content">
                                        <h4>{{ user.name }}</h4>
                                        <span>Emp ID: {{ user.empId }} </span>
                                        <span>{{ user.position }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-8" v-if="selectedUser">
                        <div class="user-logs mt-3 mt-md-0">
                            <h3 class="sub-title text-danger mb-3">
                                {{ selectedUser.name }}’s Logs Info
                            </h3>

                            <!-- Date range filters -->
                            <div
                                class="date-filters d-flex flex-wrap flex-md-nowrap mb-3"
                            >
                                <el-date-picker
                                    v-model="startDate"
                                    type="date"
                                    placeholder="Start Date"
                                >
                                </el-date-picker>
                                <el-date-picker
                                    v-model="endDate"
                                    type="date"
                                    placeholder="End Date"
                                >
                                </el-date-picker>
                                <button
                                    class="btn btn-site ml-auto"
                                    @click="filterByDate"
                                >
                                    Filter
                                </button>
                                <button
                                    class="btn btn-site bg-dark"
                                    @click="resetFilters"
                                >
                                    Reset
                                </button>
                            </div>

                            <div
                                v-if="
                                    !selectedUser.userLogs ||
                                    selectedUser.userLogs.length === 0
                                "
                            >
                                <!-- <p class="text-center">
                                    No data available for the selected date
                                    range.
                                </p> -->
                                <no-data />
                            </div>

                            <div v-else>
                                <div
                                    v-for="(
                                        log, logIndex
                                    ) in selectedUser.userLogs"
                                    :key="logIndex"
                                >
                                    <div class="user-logs-time">
                                        <span>{{ log.date }}</span>
                                    </div>
                                    <div class="user-logs__item">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="device-location">
                                                    <i
                                                        :class="log.device_icon"
                                                    ></i>
                                                </div>
                                                <ul>
                                                    <li>
                                                        Login: {{ log.loginTime
                                                        }}<sub>{{
                                                            log.loginSuffix
                                                        }}</sub>
                                                    </li>
                                                    <li>
                                                        Logout:
                                                        {{ log.logoutTime
                                                        }}<sub>{{
                                                            log.logoutSuffix
                                                        }}</sub>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="device-location">
                                                    <i class="icon-map"></i>
                                                </div>
                                                <h4>
                                                    {{ log.location }}
                                                    <span>{{
                                                        log.cityCountry
                                                    }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="col-md-8">
                        <div class="user-logs no-data py-4">
                            <img
                                src="/images/no-data.svg"
                                alt="No Data Found"
                            />
                            <h3>Select user to view Logs Info</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";
import noData from "../components/noData.vue";
export default {
    name: "UserLocation",
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            searchQuery: "",
            allUsers: [],
            selectedUser: null,
            startDate: "",
            endDate: "",
        };
    },
    computed: {
        filteredUsers() {
            if (this.searchQuery) {
                return this.allUsers.filter(
                    (user) =>
                        user.name
                            .toLowerCase()
                            .includes(this.searchQuery.toLowerCase()) ||
                        user.empId.toString().includes(this.searchQuery)
                );
            } else {
                return this.allUsers;
            }
        },
    },
    methods: {
        async fetchAllUsers() {
            this.isLoading = true;
            try {
                const response = await axios.get("/userList");
                this.allUsers = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async selectUser(user) {
            this.startDate = "";
            this.endDate = "";
            this.isLoading = true;
            try {
                const response = await axios.get(
                    `/userLocationLogs/${user.userId}`
                );
                this.selectedUser = { ...user, userLogs: response.data.data };
            } catch (error) {
                console.error("Error fetching user logs:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async filterByDate() {
            this.isLoading = true;
            try {
                const response = await axios.get(
                    `/userLocationLogs/${this.selectedUser.userId}`,
                    {
                        params: {
                            start_date: this.startDate,
                            end_date: this.endDate,
                        },
                    }
                );
                this.selectedUser.userLogs = response.data.data;
            } catch (error) {
                console.error("Error fetching user logs:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async resetFilters() {
            this.startDate = "";
            this.endDate = "";
            this.isLoading = true;
            try {
                const response = await axios.get(
                    `/userLocationLogs/${this.selectedUser.userId}`
                );
                this.selectedUser.userLogs = response.data.data;
            } catch (error) {
                console.error("Error fetching user logs:", error);
            } finally {
                this.isLoading = false;
            }
        },
    },
    created() {
        this.fetchAllUsers();
    },
};
</script>
