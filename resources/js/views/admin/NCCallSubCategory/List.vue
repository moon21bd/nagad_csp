<template>
    <div>
        <h1>Call Sub Categories</h1>
        <router-link to="/admin/call-sub-categories/create">Create New Sub Category</router-link>

        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="subCategory in callSubCategories" :key="subCategory.id">
                <td>{{ subCategory.call_sub_category_name }}</td>
                <td>{{ subCategory.call_type ? subCategory.call_type.call_type_name : '-' }}</td>
                <td>{{ subCategory.call_category ? subCategory.call_category.call_category_name : '-' }}</td>
                <td>{{ subCategory.status }}</td>
                <td>
                    <router-link :to="`/admin/call-sub-categories/edit/${subCategory.id}`">
                        <button>Edit</button>
                    </router-link>
                    <button @click="deleteSubCategory(subCategory.id)">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    computed: {
        callSubCategories() {
            return this.$store.getters.callSubCategories;
        },
    },
    created() {
        this.fetchCallSubCategories();
    },
    methods: {
        async fetchCallSubCategories() {
            try {
                await this.$store.dispatch('fetchCallSubCategories');
            } catch (error) {
                console.error('Error fetching call sub-categories:', error);
            }
        },
        async deleteSubCategory(subCategoryId) {
            if (confirm('Are you sure you want to delete this call sub-category?')) {
                try {
                    await this.$store.dispatch('deleteCallSubCategory', subCategoryId);
                    this.fetchCallSubCategories();
                } catch (error) {
                    console.error('Error deleting call sub-category:', error);
                }
            }
        }
    }
};
</script>
