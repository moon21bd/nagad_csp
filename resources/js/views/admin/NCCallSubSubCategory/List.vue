<template>
    <div>
        <h1>Call Sub Sub Categories</h1>
        <div>
            <router-link to="/admin/call-sub-sub-categories/create">
                <button>Add New Call Sub Sub Category</button>
            </router-link>
        </div>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Call Type</th>
                <th>Call Category</th>
                <th>Call Sub Category</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="subSubCategory in callSubSubCategories" :key="subSubCategory.id">
                <td>{{ subSubCategory.id }}</td>
                <td>{{ getCallTypeName(subSubCategory.call_type_id) }}</td>
                <td>{{ getCallCategoryName(subSubCategory.call_category_id) }}</td>
                <td>{{ getCallSubCategoryName(subSubCategory.call_sub_category_id) }}</td>
                <td>{{ subSubCategory.call_sub_sub_category_name }}</td>
                <td>{{ subSubCategory.status }}</td>
                <td>
                    <router-link :to="`/admin/call-sub-sub-categories/edit/${subSubCategory.id}`">
                        <button>Edit</button>
                    </router-link>
                    <button @click="deleteSubSubCategory(subSubCategory.id)">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "../../../axios";

export default {
    data() {
        return {
            callSubSubCategories: []
        };
    },

    methods: {
        async fetchCallSubSubCategories() {
            try {
                const response = await axios.get('/call-sub-sub-categories');
                this.callSubSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub-categories:', error);
            }
        },
        async deleteSubSubCategory(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                try {
                    await this.$store.dispatch('globalStore/deleteCallSubSubCategory', id);
                } catch (error) {
                    console.error('Error deleting sub sub-category:', error);
                }
            }
        }
    },
    mounted() {
        this.fetchCallSubSubCategories()
    }

};
</script>
