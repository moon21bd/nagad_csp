<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <input v-model="formData.name" type="text" placeholder="Group Name">
            <select v-model="formData.status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit">Create</button>
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
                status: 'active',
            },
            formErrors: []
        };
    },
    methods: {
        async handleSubmit() {
            try {
                this.formErrors = [];
                if (!this.formData.name) this.formErrors.push('Group Name is required.');
                if (!this.formData.status) this.formErrors.push('Status is required.');
                // If there are errors, do not submit the form
                if (this.formErrors.length > 0) return;

                // Submit the form data to the API
                const response = await axios.post('/groups', this.formData);
                console.log('Group created successfully:', response.data);

                // Clear the form data
                this.formData = {
                    name: '',
                    status: 'active',
                };

                // Navigate to the groups list route
                this.$router.push({name: 'groups'});
            } catch (error) {
                console.error('Error creating group:', error);
                if (error.response && error.response.data.errors) {
                    this.formErrors = Object.values(error.response.data.errors).flat();
                } else {
                    this.formErrors.push('Failed to create group. Please try again later.');
                }
            }
        }
    }
};
</script>
