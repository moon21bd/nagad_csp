<template>
    <div>
        <h1>Create Access List</h1>
        <form @submit.prevent="createAccess">
            <div>
                <label>Access Name</label>
                <input v-model="form.access_name" type="text" required>
            </div>
            <div>
                <label>Status</label>
                <select v-model="form.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Create</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form: {
                access_name: '',
                status: 'active',
            },
        };
    },
    methods: {
        async createAccess() {
            try {
                const response = await axios.post('/access-lists', this.form);
                console.log('Access List created successfully:', response.data);
                this.$router.push({ name: 'access-lists' });
            } catch (error) {
                console.error('Error creating access list:', error);
            }
        },
    },
};
</script>
