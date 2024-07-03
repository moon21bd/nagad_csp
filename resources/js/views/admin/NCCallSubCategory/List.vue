<template>
    <div>
        <h1>Call Sub Categories</h1>
        <router-link to="/admin/call-sub-categories/create">Create New Sub Category</router-link>
        <ul>
            <li v-for="subCategory in callSubCategories" :key="subCategory.id">
                {{ subCategory.call_sub_category_name }} - {{ subCategory.call_type.call_type_name }} -
                {{ subCategory.call_category.call_category_name }}
                <router-link :to="{ name: 'editCallSubCategory', params: { id: subCategory.id } }">Edit</router-link>
                <button @click="deleteSubCategory(subCategory.id)">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    computed: {
        callSubCategories() {
            return this.$store.getters.callSubCategories;
        }
    },
    async created() {
        await this.$store.dispatch('fetchCallSubCategories');
    },
    methods: {
        async deleteSubCategory(subCategoryId) {
            if (confirm('Are you sure you want to delete this call category?')) {
                try {
                    await this.$store.dispatch('deleteCallSubCategory', subCategoryId);
                    this.$store.dispatch('fetchCallSubCategories');
                } catch (error) {
                    console.error('Error deleting sub-category:', error);
                }
            }

        }
    }
};
</script>
