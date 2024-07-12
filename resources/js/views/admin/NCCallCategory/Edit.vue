<template>
    <div>
        <h1>Edit Call Category</h1>
        <form @submit.prevent="updateCallCategory">
            <div>
                <label for="call_type_id">Call Type:</label>
                <select v-model="category.call_type_id" required>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">{{ type.call_type_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_category_name">Category Name:</label>
                <input type="text" v-model="category.call_category_name" required/>
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="category.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Update</button>
        </form>
    </div>
</template>

<script>
import axios from "../../../axios";

export default {
    data() {
        return {
            category: {
                id: '',
                call_type_id: '',
                call_category_name: '',
                status: 'active'
            },
            callTypes: [],
            id: this.$route.params.id
        };
    },
    methods: {
        async fetchCategory() {
            try {
                const response = await axios.get('/call-categories/' + this.id);
                this.category = response.data
                console.log('response', response.data)
            } catch (error) {
                console.error('Error fetching call type:', error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async updateCallCategory() {
            try {
                const response = await axios.put(`/call-categories/${this.id}`, this.category);
                this.$router.push({name: "call-categories-index"})
            } catch (error) {
                console.error('Error updating call category:', error);
            }
        }

    },
    mounted() {
        this.fetchCallTypes()
        this.fetchCategory()
    }
};
</script>
