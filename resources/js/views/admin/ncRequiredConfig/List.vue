<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Nc Required Config</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/nc-config/add"
            ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div v-if="ncRequiredConfigs.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                            <tr>
                                <th>Call Type</th>
                                <th>Call Category </th>
                                <th>Call Sub Category</th>
                                <th>Input Filed</th>
                                <th>Input Type</th>
                                <th>Input Value</th>
                                <th>Input Validation</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="{
                                        call_type,
                                        call_category,
                                        call_sub_category,
                                        input_field_name,
                                        input_type,
                                        input_value,
                                        input_validation,
                                        status,
                                        id
                                    } in ncRequiredConfigs"
                                :key="id"
                            >
                                <td>{{  call_type ? call_type.call_type_name:""}}</td>
                                <td>
                                    {{
                                        call_category ? call_category.call_category_name:""
                                    }}
                                </td>
                                <td>
                                    {{
                                        call_sub_category ? call_sub_category.call_sub_category_name:""
                                    }}
                                </td>
                                <td>
                                    {{
                                        input_field_name
                                    }}
                                </td>
                                <td>
                                    {{
                                        input_type
                                    }}
                                </td>
                                <td>
                                    {{
                                        input_value
                                    }}
                                </td>
                                <td>
                                    {{
                                        input_validation
                                    }}
                                </td>
                                <td>
                                        <span
                                            :class="
                                                status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                        >{{ status }}</span
                                        >
                                </td>
                                <td class="text-right">
                                    <router-link
                                        class="btn-action btn-edit"
                                        :to="`/admin/nc-config/edit/${id}`"
                                    ><i class="icon-edit-pen"></i
                                    ></router-link>
                                    <a
                                        class="btn-action btn-trash"
                                        @click.prevent="
                                                deleteNcRequiredConfig(id)
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
                <no-data v-else/>
            </div>
        </div>
    </div>
</template>

<script>
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";


export default {
    components: {
        noData
    },
    data() {
        return {
            isLoading: false,
            status: "active",
            ncRequiredConfigs: []
        }
    },
    methods: {
        async fetchNcRequiredConfig() {
            try {
                const response = await axios.get('/store_config');
                this.ncRequiredConfigs = response.data;
            } catch (error) {
                console.error('Error fetching call sub-categories:', error);
            }
        },
        async deleteNcRequiredConfig(id) {

            try {
                if (confirm('Are you sure you want to delete this call sub-category?')) {
                    await axios.delete(`/store_config/${id}`);
                    await this.fetchNcRequiredConfig()
                }
            } catch (error) {
                console.error('Error deleting nc required config:', error);
            }

        }
    },
    mounted() {
        this.fetchNcRequiredConfig()
    }
};
</script>
