<template>
    <div>
        <h2>Create Group Configuration</h2>
        <form @submit.prevent="handleSubmit">
            <label>Select Group:</label>
            <select v-model="formData.group_id" required>
                <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.name }}</option>
            </select>
            <label>Access Lists:</label>
            <div v-for="accessList in accessLists" :key="accessList.id">
                <input type="checkbox" v-model="selectedAccessLists" :value="accessList.id">
                {{ accessList.access_name }}
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="formData.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            formData: {
                group_id: null,
                status: 'active',
            },
            groups: [],
            accessLists: [],
            selectedAccessLists: [], // Track selected access lists
        };
    },
    created() {
        this.fetchGroups();
        this.fetchAccessLists();
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get('/groups');
                this.groups = response.data;
            } catch (error) {
                console.error('Error fetching groups:', error);
            }
        },
        async fetchAccessLists() {
            try {
                const response = await axios.get('/access-lists');
                this.accessLists = response.data;
            } catch (error) {
                console.error('Error fetching access lists:', error);
            }
        },
        async handleSubmit() {
            try {
                const payload = {
                    group_id: this.formData.group_id,
                    status: this.formData.status,
                    access_list_ids: this.selectedAccessLists, // Use selected access lists
                };
                const response = await axios.post('/group-configs', payload);
                console.log('Group configuration saved successfully:', response.data);
                // Navigate to the groups list route
                this.$router.push({name: 'group-configs'});
                // Optionally, navigate to the list view or reset the form
            } catch (error) {
                console.error('Error saving group configuration:', error);
            }
        }
    }
};
</script>
