<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0">Dashboard</h1>
        </div>
        <div class="dashboard-card">
            <ul>
                <li>
                    <div class="img">
                        <img src="/images/tickets.svg" alt="" />
                    </div>
                    <h3><span>Open Ticket</span> 1339</h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-pending.svg" alt="" />
                    </div>
                    <h3><span>Pending Tickets </span> 135639</h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-progress.svg" alt="" />
                    </div>
                    <h3><span>Working in Progress</span> 3339</h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-closed.svg" alt="" />
                    </div>
                    <h3><span>Closed Tickets</span> 1339</h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-reopen.svg" alt="" />
                    </div>
                    <h3><span>Reopen Tickets</span> 19</h3>
                </li>
            </ul>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h2 class="sub-title text-danger m-0">
                        Monthly Tickets Count and Total Queries
                    </h2>
                    <el-select
                        class="ml-auto"
                        v-model="monthValue"
                        placeholder="Select Month"
                    >
                        <el-option
                            v-for="month in months"
                            :key="month.value"
                            :label="month.label"
                            :value="month.value"
                        >
                        </el-option>
                    </el-select>
                </div>
                <div class="row">
                    <div
                        class="col-md-12"
                        v-for="(chartOptions, index) in chartOptionsList"
                        :key="index"
                    >
                        <div class="dashboard-charts">
                            <template>
                                <vue-highcharts
                                    :options="chartOptions"
                                ></vue-highcharts>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueHighcharts from "vue2-highcharts";
export default {
    name: "Dashboard",
    components: {
        VueHighcharts,
    },
    data: () => ({
        monthValue: "",
        months: [
            {
                value: "January",
                label: "January",
            },
            {
                value: "February",
                label: "February",
            },
            {
                value: "March",
                label: "March",
            },
            {
                value: "April",
                label: "April",
            },
            {
                value: "May",
                label: "May",
            },
            {
                value: "June",
                label: "June",
            },
            {
                value: "July",
                label: "July",
            },
            {
                value: "August",
                label: "August",
            },
            {
                value: "September",
                label: "September",
            },
            {
                value: "October",
                label: "October",
            },
            {
                value: "November",
                label: "November",
            },
            {
                value: "December",
                label: "December",
            },
        ],
        chartOptionsList: [
            {
                chart: {
                    type: "column",
                },
                title: {
                    text: "Total Queries",
                    align: "left",
                    style: {
                        color: "#242526",
                        fontSize: "16px",
                        fontWeight: "500",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                credits: {
                    enabled: false,
                },
                xAxis: {
                    categories: [
                        "01",
                        "02",
                        "03",
                        "04",
                        "05",
                        "06",
                        "07",
                        "08",
                        "09",
                        "10",
                        "11",
                        "12",
                        "13",
                        "14",
                        "15",
                        "16",
                        "17",
                        "18",
                        "19",
                        "20",
                        "21",
                        "22",
                        "23",
                        "24",
                        "25",
                        "26",
                        "27",
                        "28",
                        "29",
                        "30",
                        "31",
                    ],
                    crosshair: true,
                    labels: {
                        style: {
                            color: "#345b5b",
                            fontSize: "15px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                    accessibility: {
                        description: "Days",
                    },
                },
                yAxis: {
                    title: {
                        text: "Query Count",
                        style: {
                            color: "#345b5b",
                            fontSize: "14px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                },
                legend: {
                    enabled: false,
                    itemStyle: {
                        fontSize: "16px",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                colors: ["#FF4E4E"],
                series: [
                    {
                        name: "Monthly Queries (in thousand)",
                        data: [
                            20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50, 30,
                            80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40, 50, 60,
                            80, 100, 60, 40, 50,
                        ],
                    },
                ],
            },

            {
                chart: {
                    type: "column",
                },
                title: {
                    text: "Total Compliance",
                    align: "left",
                    style: {
                        color: "#242526",
                        fontSize: "16px",
                        fontWeight: "500",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                credits: {
                    enabled: false,
                },
                xAxis: {
                    categories: [
                        "01",
                        "02",
                        "03",
                        "04",
                        "05",
                        "06",
                        "07",
                        "08",
                        "09",
                        "10",
                        "11",
                        "12",
                        "13",
                        "14",
                        "15",
                        "16",
                        "17",
                        "18",
                        "19",
                        "20",
                        "21",
                        "22",
                        "23",
                        "24",
                        "25",
                        "26",
                        "27",
                        "28",
                        "29",
                        "30",
                        "31",
                    ],
                    crosshair: true,
                    labels: {
                        style: {
                            color: "#345b5b",
                            fontSize: "15px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                    accessibility: {
                        description: "Days",
                    },
                },
                yAxis: {
                    title: {
                        text: "Query Count",
                        style: {
                            color: "#345b5b",
                            fontSize: "14px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                },
                legend: {
                    enabled: false,
                    itemStyle: {
                        fontSize: "16px",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                colors: ["#f39c12"],
                series: [
                    {
                        name: "Compliance (in thousand)",
                        data: [
                            20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50, 30,
                            80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40, 50, 60,
                            80, 100, 60, 40, 50,
                        ],
                    },
                ],
            },
            {
                chart: {
                    type: "column",
                },
                title: {
                    text: "Total Service Request",
                    align: "left",
                    style: {
                        color: "#242526",
                        fontSize: "16px",
                        fontWeight: "500",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                credits: {
                    enabled: false,
                },
                xAxis: {
                    categories: [
                        "01",
                        "02",
                        "03",
                        "04",
                        "05",
                        "06",
                        "07",
                        "08",
                        "09",
                        "10",
                        "11",
                        "12",
                        "13",
                        "14",
                        "15",
                        "16",
                        "17",
                        "18",
                        "19",
                        "20",
                        "21",
                        "22",
                        "23",
                        "24",
                        "25",
                        "26",
                        "27",
                        "28",
                        "29",
                        "30",
                        "31",
                    ],
                    crosshair: true,
                    labels: {
                        style: {
                            color: "#345b5b",
                            fontSize: "15px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                    accessibility: {
                        description: "Days",
                    },
                },
                yAxis: {
                    title: {
                        text: "Query Count",
                        style: {
                            color: "#345b5b",
                            fontSize: "14px",
                            fontFamily: "Inter, sans-serif",
                        },
                    },
                },
                legend: {
                    enabled: false,
                    itemStyle: {
                        fontSize: "16px",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                colors: ["#543884"],
                series: [
                    {
                        name: "Monthly Service Request (in thousand)",
                        data: [
                            20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50, 30,
                            80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40, 50, 60,
                            80, 100, 60, 40, 50,
                        ],
                    },
                ],
            },
        ],
    }),
};
</script>
