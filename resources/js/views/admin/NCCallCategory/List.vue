<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Call Categories</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/call-categories/create"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="callCategories.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="{
                                        call_category_name,
                                        id,
                                        status,
                                    } in callCategories"
                                    :key="id"
                                >
                                    <td>{{ call_category_name }}</td>
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
                                            :to="{
                                                name: 'call-categories-edit',
                                                params: { id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="deleteCategory(id)"
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
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";
export default {
    components: {
        noData,
    },
    name: "List",
    data() {
        return {
            isLoading: false,
        };
    },
    computed: {
        callCategories() {
            return this.$store.getters.callCategories;
        },
    },
    created() {
        this.$store.dispatch("fetchCallCategories");
    },
    methods: {
        deleteCategory(categoryId) {
            if (
                confirm("Are you sure you want to delete this call category?")
            ) {
                this.$store.dispatch("deleteCallCategory", categoryId);
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable();
            });
        },
    },
    watch: {
        callCategories(newValue, oldValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
