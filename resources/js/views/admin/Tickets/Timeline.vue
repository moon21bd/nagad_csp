<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0">Ticket I’d : H025-85452</h1>
            <div class="btn btn-site btn-sm ml-auto">
                <i class="icon-check-circle"></i> Valid
            </div>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="sub-title text-danger mb-3">
                            Ticket Timeline
                        </h2>
                        <el-timeline class="m-0 p-0">
                            <el-timeline-item
                                v-for="(activity, index) in activities"
                                :key="index"
                                :icon="activity.icon"
                                :type="activity.type"
                                :color="activity.color"
                                :size="activity.size"
                                :timestamp="activity.timestamp"
                            >
                                {{ activity.content }}
                            </el-timeline-item>
                        </el-timeline>
                    </div>
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>735439</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Customer Name</td>
                                        <td>Sakib Kaji</td>
                                    </tr>
                                    <tr>
                                        <td>Subject Name</td>
                                        <td>Merchant Payment Related</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="alert alert-danger text-center"
                            role="alert"
                        >
                            Tansferred to Zone/Sub-zone/HQ
                        </div>
                        <h2 class="sub-title text-danger">Comments</h2>
                        <div class="comments">
                            <ul class="list-unstyled">
                                <li class="media">
                                    <img
                                        class="mr-3"
                                        src="/images/user-avatar.png"
                                        alt="Generic placeholder image"
                                    />
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1">
                                            Cras purus odio
                                            <span>Aug 7, 2024</span>
                                        </h5>
                                        <p>
                                            Cras sit amet nibh libero, in
                                            gravida nulla. Nulla vel metus
                                            scelerisque ante sollicitudin. Cras
                                            purus odio.
                                        </p>
                                    </div>
                                </li>
                                <li class="media">
                                    <img
                                        class="mr-3"
                                        src="/images/user-avatar.png"
                                        alt="Generic placeholder image"
                                    />
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1">
                                            List-based media object
                                            <span>Aug 7, 2024</span>
                                        </h5>
                                        <p>
                                            Tempus viverra turpis. Fusce
                                            condimentum nunc ac nisi vulputate
                                            fringilla. Donec lacinia congue
                                            felis in faucibus.
                                        </p>
                                    </div>
                                </li>
                                <li class="media">
                                    <img
                                        class="mr-3"
                                        src="/images/user-avatar.png"
                                        alt="Generic placeholder image"
                                    />
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1">
                                            Cras sit amet nibh libero
                                            <span>Aug 7, 2024</span>
                                        </h5>
                                        <p>
                                            Cras purus odio, vestibulum in
                                            vulputate at, tempus viverra turpis.
                                            Fusce condimentum nunc ac nisi
                                            vulputate fringilla
                                        </p>
                                    </div>
                                </li>
                            </ul>
                            <div class="comments-form">
                                <form action="">
                                    <div class="d-flex align-items-center">
                                        <img
                                            class="mr-3"
                                            src="/images/user-avatar.png"
                                            alt="Generic placeholder image"
                                        />
                                        <Textarea
                                            class="form-control"
                                            placeholder="Add a comment…"
                                            col="2"
                                        ></Textarea>
                                    </div>
                                    <div class="text-right">
                                        <button
                                            class="btn btn-send mt-3"
                                            type="button"
                                        >
                                            Send
                                        </button>
                                    </div>
                                </form>
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
    data() {
        return {
            isLoading: false,
            activities: [
                {
                    content: "Custom icon",
                    timestamp: "2018-04-12 20:46",
                    color: "#FF4E4E",
                },
                {
                    content: "Custom color",
                    timestamp: "2018-04-03 20:46",
                    color: "#FF4E4E",
                },
                {
                    content: "Custom size",
                    timestamp: "2018-04-03 20:46",
                    size: "large",
                },
                {
                    content: "Default node",
                    timestamp: "2018-04-03 20:46",
                },
            ],
        };
    },
    methods: {},
};
</script>
<style scoped>
.btn-site {
    background: #16a085;
    cursor: default !important;
}
.btn-send {
    background: #2c3e50;
    color: #fff;
}
</style>
