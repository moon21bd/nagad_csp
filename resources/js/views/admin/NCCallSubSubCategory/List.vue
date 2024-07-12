<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Call Sub Sub Categories</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/call-sub-sub-categories/create"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="callSubSubCategories.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Call Type</th>
                                    <th>Call Category</th>
                                    <th>Call Sub Category</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="subSubCategory in callSubSubCategories"
                                    :key="subSubCategory.id"
                                >
                                    <td>{{ subSubCategory.id }}</td>
                                    <td>
                                        {{
                                            getCallTypeName(
                                                subSubCategory.call_type_id
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            getCallCategoryName(
                                                subSubCategory.call_category_id
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            getCallSubCategoryName(
                                                subSubCategory.call_sub_category_id
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            subSubCategory.call_sub_sub_category_name
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            :class="
                                                subSubCategory.status ===
                                                'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{ subSubCategory.status }}</span
                                        >
                                    </td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="`/admin/call-sub-sub-categories/edit/${subSubCategory.id}`"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteSubSubCategory(
                                                    subSubCategory.id
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
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";
export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            status: "active",
        };
    },
    computed: {
        callSubSubCategories() {
            return this.$store.getters.allCallSubSubCategories;
        },
        callTypes() {
            return this.$store.getters.callTypes;
        },
        callCategories() {
            return this.$store.getters.callCategories;
        },
        callSubCategories() {
            return this.$store.getters.callSubCategories;
        },
    },
    methods: {
        getCallTypeName(callTypeId) {
            const type = this.callTypes.find((type) => type.id === callTypeId);
            return type ? type.call_type_name : "Unknown";
        },
        getCallCategoryName(callCategoryId) {
            const category = this.callCategories.find(
                (category) => category.id === callCategoryId
            );
            return category ? category.call_category_name : "Unknown";
        },
        getCallSubCategoryName(callSubCategoryId) {
            const subCategory = this.callSubCategories.find(
                (subCategory) => subCategory.id === callSubCategoryId
            );
            return subCategory ? subCategory.call_sub_category_name : "Unknown";
        },
        async deleteSubSubCategory(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                try {
                    await this.$store.dispatch("deleteCallSubSubCategory", id);
                } catch (error) {
                    console.error("Error deleting sub sub-category:", error);
                }
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable();
            });
        },
    },
    created() {
        this.$store.dispatch("fetchCallSubSubCategories");
        this.$store.dispatch("fetchCallTypes");
        this.$store.dispatch("fetchCallCategories");
        this.$store.dispatch("fetchCallSubCategories");
    },
    watch: {
        callSubSubCategories(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
