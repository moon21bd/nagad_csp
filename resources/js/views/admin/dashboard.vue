<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0 mr-2">Dashboard</h1>
            <el-select v-model="filterGroup" placeholder="Select Group">
                <el-option
                    v-for="group in groups"
                    :key="group.id"
                    :label="group.name"
                    :value="group.id"
                >
                </el-option>
            </el-select>
        </div>
        <div class="dashboard-card">
            <ul>
                <li>
                    <div class="img">
                        <img src="/images/tickets.svg" alt="" />
                    </div>
                    <h3>
                        <span>Open Ticket</span>
                        {{ totalReportCount.openTicket ?? 0 }}
                    </h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-pending.svg" alt="" />
                    </div>
                    <h3>
                        <span>Pending Tickets </span
                        >{{ totalReportCount.pendingTicket ?? 0 }}
                    </h3>
                </li>
                <li class="progress-sla">
                    <div class="img">
                        <img src="/images/tickets-progress.svg" alt="" />
                    </div>
                    <h3>
                        <span>In progress With SLA</span
                        >{{ totalReportCount.inProgressTicket ?? 0 }}
                    </h3>
                </li>
                <li class="failed-sla">
                    <div class="img">
                        <img src="/images/tickets-progress.svg" alt="" />
                    </div>
                    <h3>
                        <span>In progress Failed SLA</span
                        >{{ totalReportCount.inProgressTicket ?? 0 }}
                    </h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-closed.svg" alt="" />
                    </div>
                    <h3>
                        <span>Closed Tickets</span
                        >{{ totalReportCount.closedTicket ?? 0 }}
                    </h3>
                </li>
                <li>
                    <div class="img">
                        <img src="/images/tickets-reopen.svg" alt="" />
                    </div>
                    <h3>
                        <span>Reopen Tickets</span
                        >{{ totalReportCount.reopenTicket ?? 0 }}
                    </h3>
                </li>
            </ul>
        </div>

        <div class="row card-equal">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h2 class="sub-title text-danger m-0">
                                Ticket Status
                            </h2>
                            <el-select
                                class="ml-auto"
                                v-model="monthTickets"
                                placeholder="Select Month"
                                @change="fetchMonthWiseTicketStatus"
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
                        <div class="dashboard-charts">
                            <vue-highcharts
                                ref="pieChart"
                                :options="ticketsStatus"
                            ></vue-highcharts>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="sub-title text-danger mb-5">Today Status</h2>
                        <div class="today-status">
                            <ul>
                                <li class="open-tickets">
                                    <h3>
                                        <span>Total Ticket</span
                                        >{{ dailyReportCount.totalCount ?? 0 }}
                                    </h3>
                                    <i class="icon-tickets"></i>
                                </li>
                                <li class="progress-tickets">
                                    <h3>
                                        <span>Work in Progress</span
                                        >{{
                                            dailyReportCount.totalInProgressTicket ??
                                            0
                                        }}
                                    </h3>
                                    <i class="icon-loader"></i>
                                </li>
                                <li class="close-tickets">
                                    <h3>
                                        <span>Close Ticket</span
                                        >{{
                                            dailyReportCount.totalClosedTicket ??
                                            0
                                        }}
                                    </h3>
                                    <i class="icon-check-circle"></i>
                                </li>
                                <li class="total-query">
                                    <h3>
                                        <span>Total Query</span
                                        >{{ dailyReportCount.totalQuery ?? 0 }}
                                    </h3>
                                    <i class="icon-help"></i>
                                </li>
                                <li class="total-compliance">
                                    <h3>
                                        <span>Total Compliance</span
                                        >{{
                                            dailyReportCount.totalComplaint ?? 0
                                        }}
                                    </h3>
                                    <i class="icon-alert-triangle"></i>
                                </li>
                                <li class="total-tequest">
                                    <h3>
                                        <span>Total S.Request</span
                                        >{{
                                            dailyReportCount.totalServiceRequest ??
                                            0
                                        }}
                                    </h3>
                                    <i class="icon-headphones"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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
                        @change="fetchColumnChartData"
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
                        v-for="(chartOptions, index) in totalQueries"
                        :key="index"
                    >
                        <div class="dashboard-charts">
                            <vue-highcharts
                                ref="columnChart2"
                                :options="chartOptions"
                            ></vue-highcharts>
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
    data() {
        return {
            groups: [],
            monthValue: "",
            monthTickets: "",
            filterGroup: "",
            totalReportCount: {},
            dailyReportCount: {},
            monthWiseReportCount: {},
            chartInstance: null,
            userGroupId: 1,
            columnChartData: {
                totalComplaintData: "",
                totalQueryData: "",
                totalServiceRequestData: "",
            },
            queryColumnChartData: "",
            complaintColumnChartData: "",
            serviceRequestColumnChartData: "",
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
            ticketsStatus: {
                chart: {
                    type: "pie",
                    options3d: {
                        enabled: true,
                        alpha: 45,
                    },
                },

                legend: {
                    enabled: true,
                    layout: "vertical",
                    align: "right",
                    verticalAlign: "middle",
                    itemMarginTop: 10,
                    itemMarginBottom: 10,
                    labelFormatter: function () {
                        return `${this.percentage.toFixed(0)}% - ${this.name}`;
                    },
                    style: {
                        fontSize: "14px",
                        fontFamily: "Inter, sans-serif",
                        color: "#003049",
                    },
                },
                title: {
                    text: null,
                },
                credits: {
                    enabled: true,
                },
                plotOptions: {
                    pie: {
                        innerSize: "50%",
                        depth: 45,
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            distance: -30,
                            format: "{point.percentage:.0f}%",
                            style: {
                                fontSize: "16px",
                                fontFamily: "Inter, sans-serif",
                                color: "white",
                                textOutline: "none",
                            },
                        },
                        showInLegend: true,
                    },
                    series: {
                        allowPointSelect: true,
                        cursor: "pointer",
                        borderRadius: 8,
                    },
                },
                series: [
                    {
                        name: "Status",
                        colorByPoint: true,
                        data: [
                            {
                                name: "Open Ticket",
                                y: 0,
                                color: "#003049",
                            },
                            {
                                name: "In Progress Ticket",
                                y: 20,
                                color: "#D62828",
                            },
                            {
                                name: "Resolved Ticket",
                                y: 167,
                                color: "#F77F00",
                            },
                            {
                                name: "Close Ticket",
                                y: 50,
                                color: "#EC4176",
                            },
                            {
                                name: "Reopen Ticket",
                                y: 20,
                                color: "#543884",
                            },
                        ],
                    },
                ],
            },
            totalQueries: [
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
                                20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50,
                                30, 80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40,
                                50, 60, 80, 100, 60, 40, 50,
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
                            text: "Complaint Count",
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
                                20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50,
                                30, 80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40,
                                50, 60, 80, 100, 60, 40, 50,
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
                            text: "Service Request Count",
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
                                20, 40, 50, 20, 40, 50, 60, 80, 100, 60, 40, 50,
                                30, 80, 100, 20, 40, 50, 60, 80, 40, 50, 20, 40,
                                50, 60, 80, 100, 60, 40, 50,
                            ],
                        },
                    ],
                },
            ],
        };
    },

    mounted() {
        this.init();
        this.initializeChart();
        this.fetchGroups();
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async init() {
            this.userGroupId = this.$store.state.auth.user.group_id;
            await this.fetchTotalReportCount();
            await this.fetchDailyReportCount();
            await this.fetchColumnChartData();
        },
        async initializeChart() {
            await this.$nextTick();
            // Attempt to access the chart instance
            this.chartInstance = this.$refs.pieChart?.chart;
            // this.columnChartInstance = this.$refs.columnChart2?.chart;
            if (this.chartInstance) {
                // console.log("Chart instance is ready");
            } else {
                // console.error("Chart instance could not be initialized");
            }
        },
        updateCharts(newData) {
            this.$nextTick(() => {
                newData.forEach((chartOptions, index) => {
                    const chartRef = this.$refs.columnChart2[index];
                    if (chartRef && chartRef.chart) {
                        chartRef.chart.update(chartOptions, true, true);
                    }
                });
            });
        },
        async fetchTotalReportCount() {
            try {
                const response = await axios.get(
                    `/get-total-report-count/${this.userGroupId}`
                );
                this.totalReportCount = response.data;
                this.monthWiseReportCount = response.data;
                await this.setTicketStatusValue(this.monthWiseReportCount);
            } catch (error) {
                console.error("Error fetching Total Report Count:", error);
            }
        },
        async fetchDailyReportCount() {
            try {
                const response = await axios.get(
                    `/get-daily-report-count/${this.userGroupId}`
                );
                this.dailyReportCount = response.data;
                // console.log("daily Report", this.dailyReportCount);
            } catch (error) {
                // console.error("Error fetching daily report count:", error);
            }
        },
        async fetchMonthWiseTicketStatus() {
            try {
                const response = await axios.get(
                    `/get-month-wise-ticket-status-count/${this.userGroupId}/${this.monthTickets}`
                );
                this.monthWiseReportCount = response.data;
                await this.setTicketStatusValue(this.monthWiseReportCount);
            } catch (error) {
                console.error("Error fetching service sub categories:", error);
            }
        },
        async fetchColumnChartData() {
            try {
                const response = await axios.get(
                    `/get-date-wise-column-chart-data-count/${this.userGroupId}/${this.monthValue}`
                );
                this.columnChartData = response.data;
                this.queryColumnChartData = Object.values(
                    this.columnChartData.totalQueryData
                );
                this.complaintColumnChartData = Object.values(
                    this.columnChartData.totalComplaintData
                );
                this.serviceRequestColumnChartData = Object.values(
                    this.columnChartData.totalServiceRequestData
                );

                this.totalQueries[0].series[0].data = this.queryColumnChartData;
                this.totalQueries[1].series[0].data =
                    this.complaintColumnChartData;
                this.totalQueries[2].series[0].data =
                    this.serviceRequestColumnChartData;
                this.updateCharts(this.totalQueries);
            } catch (error) {
                console.error("Error fetching service sub categories:", error);
            }
        },

        async setTicketStatusValue(data) {
            if (this.chartInstance) {
                this.chartInstance.series[0].setData([
                    {
                        name: "Open Ticket",
                        y: data.openTicket ?? 0,
                        color: "#003049",
                    },
                    {
                        name: "In Progress Ticket",
                        y: data.inProgressTicket ?? 0,
                        color: "#D62828",
                    },
                    {
                        name: "Pending Ticket",
                        y: data.pendingTicket ?? 0,
                        color: "#F77F00",
                    },
                    {
                        name: "Close Ticket",
                        y: data.closedTicket ?? 0,
                        color: "#EC4176",
                    },
                    {
                        name: "Reopen Ticket",
                        y: data.reopenTicket ?? 0,
                        color: "#543884",
                    },
                ]);
            }
        },
    },
};
</script>
<style></style>
