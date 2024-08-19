<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Service Type Configurations</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'service-type-config-add' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="serviceTypeConfigs.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Sub-Category</th>
                                    <th>Group With TATs</th>
                                    <th>Escalation</th>
                                    <th>Show Attachment</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="config in serviceTypeConfigs"
                                    :key="config.id"
                                >
                                    <td>{{ config.id }}</td>
                                    <td>
                                        {{
                                            config.call_type?.call_type_name ||
                                            ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            config.call_category
                                                ?.call_category_name || ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            config.call_sub_category
                                                ?.call_sub_category_name || ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            jsonString(config.responsible_group)
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            capitalizedMessage(
                                                config.is_escalation
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            capitalizedMessage(
                                                config.is_show_attachment
                                            )
                                        }}
                                    </td>

                                    <td>
                                        <span
                                            :class="
                                                config.status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{
                                                capitalizedMessage(
                                                    config.status
                                                )
                                            }}</span
                                        >
                                    </td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'service-type-config-edit',
                                                params: { id: config.id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteServiceTypeConfig(
                                                    config.id
                                                )
                                            "
                                        >
                                            <i class="icon-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <no-data v-else />
            </div>
        </div>
    </div>
</template>

<script>
import _ from "lodash";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";
import { capitalize } from "../../../utils/common";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            status: "active",
            serviceTypeConfigs: [],
        };
    },
    methods: {
        /* capitalize(str) {
            return _.capitalize(str);
        }, */
        capitalizedMessage(str) {
            return capitalize(str);
        },
        jsonString(data) {
            const transformedData = data.map((item) => ({
                id: item.id,
                name: item.group_name,
                tatHours: item.tat_hours,
            }));

            return JSON.stringify(transformedData, null, 2);
        },
        async fetchServiceTypeConfigs() {
            this.isLoading = true;
            try {
                const response = await axios.get("/service-type-config");
                console.log("fetchServiceTypeConfigs-response", response);
                this.serviceTypeConfigs = response.data;
            } catch (error) {
                console.error("Error fetching call sub-categories:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async deleteServiceTypeConfig(id) {
            try {
                if (
                    confirm(
                        "Are you sure you want to delete this configuration?"
                    )
                ) {
                    await axios.delete(`/service-type-config/${id}`);
                    await this.fetchServiceTypeConfigs();
                }
            } catch (error) {
                console.error("Error deleting service type config:", error);
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable({
                    order: [[0, "desc"]],
                });
            });
        },
    },
    mounted() {
        this.fetchServiceTypeConfigs();
    },
    watch: {
        serviceTypeConfigs(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
<style>
.table.dataTable > thead > tr > th {
    white-space: nowrap;
}
.table > thead > tr > th:last-child,
.table > tbody > tr > td:last-child {
    white-space: nowrap;
    position: sticky;
    right: 0;
    background: #fff;
}
.table > thead > tr > th:last-child {
    background: #fff9f9;
}
</style>
