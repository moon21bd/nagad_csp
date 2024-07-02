<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <input v-model="formData.name" type="text" placeholder="Group Name">
            <input v-model.number="formData.role_id" type="number" placeholder="Role ID Mapping">
            <select v-model="formData.status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <input v-model.number="formData.created_by" type="number" placeholder="Created By">
            <button type="submit">Update Group</button>
        </form>

        <ul v-if="formErrors.length">
            <li v-for="error in formErrors" :key="error">{{ error }}</li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            formData: {
                name: '',
                role_id: null,
                status: 'active',
                created_by: null
            },
            formErrors: []
        };
    },
    methods: {
        async handleSubmit() {
            try {
                this.formErrors = [];
                if (!this.formData.name) this.formErrors.push('Group Name is required.');
                if (!this.formData.role_id) this.formErrors.push('Role ID Mapping is required.');
                if (!this.formData.status) this.formErrors.push('Status is required.');
                if (!this.formData.created_by) this.formErrors.push('Created By is required.');

                // If there are errors, do not submit the form
                if (this.formErrors.length > 0) return;

                // Submit the form data to the API
                const groupId = this.$route.params.id; // Assuming you have groupId from route params
                const response = await axios.put(`/groups/${groupId}`, this.formData);
                console.log('Group updated successfully:', response.data);

                // Clear the form data (optional, based on your UX flow)
                this.formData = {
                    name: '',
                    role_id: null,
                    status: 'active',
                    created_by: null
                };

                // Navigate to the groups list route or other appropriate route
                this.$router.push({ name: 'groups' });
            } catch (error) {
                console.error('Error updating group:', error);
                if (error.response && error.response.data.errors) {
                    this.formErrors = Object.values(error.response.data.errors).flat();
                } else {
                    this.formErrors.push('Failed to update group. Please try again later.');
                }
            }
        },
        async fetchGroupData() {
            try {
                const groupId = this.$route.params.id; // Assuming you have groupId from route params
                const response = await axios.get(`/groups/${groupId}`);
                this.formData = {
                    name: response.data.name,
                    role_id: response.data.role_id,
                    status: response.data.status,
                    created_by: response.data.created_by
                };
            } catch (error) {
                console.error('Error fetching group data:', error);
                // Handle error appropriately, e.g., show error message to user
            }
        }
    },
    created() {
        // Fetch group data when component is created
        this.fetchGroupData();
    }
};
</script>
