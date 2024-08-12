<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title">User Location</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <form action="" class="verify-user">
                            <input
                                class="form-control"
                                type="tel"
                                v-model="userId"
                                name="customer"
                                placeholder="Customer Account No"
                                required
                            />
                            <button class="btn">
                                <i class="icon-search"></i>
                            </button>
                        </form>
                        <div class="user-search">
                            <ul>
                                <li>
                                    <div class="user-search-img">
                                        <img
                                            src="/images/user-avatar.png"
                                            alt=""
                                        />
                                    </div>
                                    <div class="user-search-content">
                                        <h4>Kazi Sakib</h4>
                                        <span>Emp ID: 25836 </span>
                                        <span>Digicon-CC Agent</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="user-logs">
                            <h3 class="title text-center text-danger mb-3">
                                Kazi Sakib’s Logs Info
                            </h3>
                            <div class="user-logs-time">
                                <span>Wednesday, Jun 23, 2021</span>
                            </div>
                            <div class="user-logs__item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="device-location">
                                            <i class="icon-laptop"></i>
                                        </div>
                                        <ul>
                                            <li>Login: 02.30<sub>pm</sub></li>
                                            <li>Logout: 05.45<sub>pm</sub></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="device-location">
                                            <i class="icon-map"></i>
                                        </div>
                                        <h4>
                                            Mirpur 12
                                            <span>Dhaka, Bangladesh</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="user-logs-time">
                                <span>Wednesday, Jun 23, 2021</span>
                            </div>
                            <div class="user-logs__item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="device-location">
                                            <i class="icon-laptop"></i>
                                        </div>
                                        <ul>
                                            <li>Login: 02.30<sub>pm</sub></li>
                                            <li>Logout: 05.45<sub>pm</sub></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="device-location">
                                            <i class="icon-map"></i>
                                        </div>
                                        <h4>
                                            Mirpur 12
                                            <span>Dhaka, Bangladesh</span>
                                        </h4>
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
export default {
    name: "User Location",
    data() {
        return {
            isLoading: false,
            userId: "",
        };
    },
};
</script>
