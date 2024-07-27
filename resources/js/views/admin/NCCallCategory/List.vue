<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Service Categories</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'service-categories-create' }"
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Service Type</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="callCategory in callCategories"
                                    :key="callCategory.id"
                                >
                                    <td>
                                        {{ callCategory.id }}
                                    </td>
                                    <td>
                                        {{ callCategory.call_category_name }}
                                    </td>
                                    <td>
                                        {{
                                            callCategory.call_type
                                                ?.call_type_name || "N/A"
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            :class="
                                                callCategory.status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{ callCategory.status }}</span
                                        >
                                    </td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'service-categories-edit',
                                                params: { id: callCategory.id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteCategory(callCategory.id)
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
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";

export default {
    name: "List",
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            callCategories: [],
        };
    },

    methods: {
        async fetchCallCategories() {
            try {
                const response = await axios.get("/call-categories");
                console.log("response.data", response.data);
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching service categories:", error);
            }
        },
        async deleteCategory(categoryId) {
            try {
                if (
                    confirm(
                        "Are you sure you want to delete this service category?"
                    )
                ) {
                    await axios.delete(`/call-categories/${categoryId}`);
                    this.fetchCallCategories();
                }
            } catch (error) {
                console.error("Error deleting service category:", error);
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
        this.fetchCallCategories();
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
