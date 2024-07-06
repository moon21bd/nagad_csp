<template>
    <div>
        <h1>Access Lists</h1>
        <router-link to="/admin/access-lists/create">Create New Access List</router-link>
        <ul>
            <li v-for="access in accessLists" :key="access.id">
                {{ access.access_name }} - {{ access.status }}
                <router-link :to="{ name: 'edit-access', params: { id: access.id } }">Edit</router-link>
                <button @click="deleteAccess(access.id)">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            accessLists: [],
        };
    },
    async created() {
        try {
            const response = await axios.get('/access-lists');
            this.accessLists = response.data;
        } catch (error) {
            console.error('Error fetching access lists:', error);
        }
    },
    methods: {
        async deleteAccess(id) {
            if (confirm('Are you sure you want to delete this access list?')) {
                try {
                    await axios.delete(`/access-lists/${id}`);
                    this.accessLists = this.accessLists.filter(access => access.id !== id);
                } catch (error) {
                    console.error('Error deleting access list:', error);
                }
            }
        }
    },
};
</script>
