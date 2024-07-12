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
import axios from "../../../axios";

export default {
    name: 'List',
    data() {
        return {
            callCategories: []
        }
    },
    methods: {
        async fetchCallCategories() {
            try {
                const response = await axios.get("/call-categories");
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async deleteCategory(categoryId) {
            try {
                if (confirm('Are you sure you want to delete this call category?')) {
                    await axios.delete(`/call-categories/${categoryId}`);
                    this.fetchCallCategories()
                }
            } catch (error) {
                console.error('Error deleting call category:', error);
            }
        },
    },
    mounted() {
        this.fetchCallCategories()
    }
};
</script>
