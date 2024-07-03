<template>
    <div>
        <h1>Create Call Category</h1>
        <form @submit.prevent="createCategory">
            <div>
                <label for="call_type_id">Call Type:</label>
                <select v-model="category.call_type_id" required>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">{{ type.call_type_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_category_name">Category Name:</label>
                <input type="text" v-model="category.call_category_name" required />
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="category.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Create</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            category: {
                call_type_id: '',
                call_category_name: '',
                status: 'active'
            }
        };
    },
    computed: {
        callTypes() {
            return this.$store.getters.callTypes;
        }
    },
    async created() {
        await this.$store.dispatch('fetchCallTypes');
    },
    methods: {
        async createCategory() {
            try {
                await this.$store.dispatch('createCallCategory', this.category);
                this.$router.push('/admin/call-categories');
            } catch (error) {
                console.error('Error creating category:', error);
            }
        }
    }
};
</script>
