<template>
    <div>
        <h1>Edit Access List</h1>
        <form @submit.prevent="updateAccess">
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
            <button type="submit">Update</button>
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
    async created() {
        try {
            const accessId = this.$route.params.id;
            const response = await axios.get(`/access-lists/${accessId}`);
            this.form = response.data;
        } catch (error) {
            console.error('Error fetching access list:', error);
        }
    },
    methods: {
        async updateAccess() {
            try {
                const accessId = this.$route.params.id;
                const response = await axios.put(`/access-lists/${accessId}`, this.form);
                console.log('Access List updated successfully:', response.data);
                this.$router.push({ name: 'access-lists' });
            } catch (error) {
                console.error('Error updating access list:', error);
            }
        },
    },
};
</script>
