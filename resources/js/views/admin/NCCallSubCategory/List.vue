<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Call Sub Categories</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/call-sub-categories/create"
            ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div v-if="callSubCategories.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                            <tr>
                                <th>Sub Category</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="{
                                        call_sub_category_name,
                                        call_type,
                                        call_category,
                                        id,
                                        status,
                                    } in callSubCategories"
                                :key="id"
                            >
                                <td>{{ call_sub_category_name }}</td>
                                <td>
                                    {{
                                        call_type
                                            ? call_type.call_type_name
                                            : "-"
                                    }}
                                </td>
                                <td>
                                    {{
                                        call_category
                                            ? call_category.call_category_name
                                            : "-"
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
                                        :to="`/admin/call-sub-categories/edit/${id}`"
                                    ><i class="icon-edit-pen"></i
                                    ></router-link>
                                    <a
                                        class="btn-action btn-trash"
                                        @click.prevent="
                                                deleteSubCategory(id)
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
import axios from "../../../axios";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            status: "active",
            callSubCategories: []
        }
    },
    methods: {
        async fetchCallSubCategories() {
            try {
                const response = await axios.get('/call-sub-categories');
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub-categories:', error);
            }
        },
        async deleteSubCategory(subCategoryId) {

            try {
                if (confirm('Are you sure you want to delete this call sub-category?')) {
                    await axios.delete(`/call-sub-categories/${subCategoryId}`);
                    this.fetchCallSubCategories()
                }
            } catch (error) {
                console.error('Error deleting call sub-category:', error);
            }

        }
    },
    mounted() {
        this.fetchCallSubCategories()
    }
};
</script>
