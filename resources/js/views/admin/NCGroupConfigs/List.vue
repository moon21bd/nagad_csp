<template>
    <div>
        <h2>Group Configurations</h2>

        <router-link :to="{ name: 'create-group-config' }" class="btn btn-primary mb-3">Add Group Configuration</router-link>

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Group Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="groupConfig in groupConfigs" :key="groupConfig.id">
                <td>{{ groupConfig.id }}</td>
                <td>{{ groupConfig.group.name }}</td>
                <td>{{ groupConfig.status }}</td>
                <td>
                    <!-- Button to edit group configuration -->
                    <router-link :to="{ name: 'edit-group-config', params: { id: groupConfig.id } }" class="btn btn-sm btn-primary">Edit</router-link>

                    <!-- Button to delete group configuration (if required) -->
                    <button @click="confirmDelete(groupConfig.id)" class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            groupConfigs: []
        };
    },
    created() {
        this.fetchGroupConfigs();
    },
    methods: {
        fetchGroupConfigs() {
            axios.get('/group-configs')
                .then(response => {
                    this.groupConfigs = response.data;
                })
                .catch(error => {
                    console.error('Error fetching group configurations:', error);
                });
        },
        confirmDelete(groupId) {
            if (confirm('Are you sure you want to delete this group configuration?')) {
                axios.delete(`/group-configs/${groupId}`)
                    .then(() => {
                        // Optional: Remove deleted item from local data to reflect changes immediately
                        this.groupConfigs = this.groupConfigs.filter(item => item.id !== groupId);
                        console.log('Group configuration deleted successfully.');
                    })
                    .catch(error => {
                        console.error('Error deleting group configuration:', error);
                    });
            }
        }
    }
};
</script>
