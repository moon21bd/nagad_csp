<template>
    <div>
        <h1>Call Categories</h1>
        <router-link to="/admin/call-categories/create">Create New Category</router-link>
        <ul>
            <li v-for="category in callCategories" :key="category.id">
                {{ category.call_category_name }} - {{ category.status }}
                <router-link :to="{ name: 'call-categories-edit', params: { id: category.id } }">Edit</router-link>
                <button @click="deleteCategory(category.id)">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'List',
    computed: {
        callCategories() {
            return this.$store.getters.callCategories;
        }
    },
    created() {
        this.$store.dispatch('fetchCallCategories');
    },
    methods: {
        deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this call category?')) {
                this.$store.dispatch('deleteCallCategory', categoryId);
            }
        }
    }
};
</script>
