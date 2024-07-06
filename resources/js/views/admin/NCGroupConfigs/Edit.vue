<template>
    <div>
        <h2>Edit Group Configuration</h2>
        <form @submit.prevent="handleSubmit">
            <label>Select Group:</label>
            <select v-model="formData.group_id" required>
                <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.name }}</option>
            </select>

            <label>Access Lists:</label>
            <div v-for="accessList in accessLists" :key="accessList.id">
                <input type="checkbox"
                       :id="'accessList_' + accessList.id"
                       v-model="selectedAccessLists"
                       :value="accessList.id">
                <label :for="'accessList_' + accessList.id">{{ accessList.access_name }}</label>
            </div>

            <div>
                <label for="status">Status:</label>
                <select v-model="formData.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit">Update</button>
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
            groupConfigId: null, // Group configuration ID passed via route params
        };
    },
    created() {
        this.groupConfigId = this.$route.params.id;
        this.fetchGroupConfig();
        this.fetchGroups(); // Fetch all groups
        this.fetchAccessLists(); // Fetch all access lists
    },
    methods: {
        async fetchGroupConfig() {
            try {
                const response = await axios.get(`/group-configs/${this.groupConfigId}`);
                console.log('Fetched group config:', response.data); // Debugging line

                if (response.data) {
                    this.formData.group_id = response.data.group_id;
                    this.formData.status = response.data.status;

                    // Extract access list IDs from the response and set them to selectedAccessLists
                    if (response.data.access_lists && Array.isArray(response.data.access_lists)) {
                        this.selectedAccessLists = response.data.access_lists.map(access => access.id);
                        console.log('Selected access lists:', this.selectedAccessLists); // Debugging line
                    } else {
                        console.error('Access lists are not returned or are not in the expected format');
                    }
                } else {
                    console.error('No data returned for group configuration');
                }
            } catch (error) {
                console.error('Error fetching group configuration:', error);
            }
        },
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
                console.log('Fetched access lists:', this.accessLists); // Debugging line
            } catch (error) {
                console.error('Error fetching access lists:', error);
            }
        },
        async handleSubmit() {
            try {
                const payload = {
                    group_id: this.formData.group_id,
                    status: this.formData.status,
                    access_list_ids: this.selectedAccessLists,
                };
                await axios.put(`/group-configs/${this.groupConfigId}`, payload);
                console.log('Group configuration updated successfully');
                this.$router.push('/admin/group-configs');
            } catch (error) {
                console.error('Error updating group configuration:', error);
            }
        }
    }
};
</script>
