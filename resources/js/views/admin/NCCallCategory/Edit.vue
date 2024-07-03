<template>
    <div>
        <h1>Edit Call Category</h1>
        <form @submit.prevent="updateCategory">
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
            <button type="submit">Update</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            category: {
                id: '',
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
        await this.fetchCategory();
    },
    methods: {
        async fetchCategory() {
            try {
                const categoryId = this.$route.params.id;
                const categories = await this.$store.dispatch('fetchCallCategories');
                this.category = categories.find(category => category.id === parseInt(categoryId));
                if (!this.category) {
                    console.error('Category not found');
                    this.$router.push('/admin/nc-call-categories');
                }
            } catch (error) {
                console.error('Error fetching category:', error);
            }
        },
        async updateCategory() {
            try {
                await this.$store.dispatch('updateCallCategory', this.category);
                this.$router.push('/admin/call-categories');
            } catch (error) {
                console.error('Error updating category:', error);
            }
        }
    }
};
</script>
