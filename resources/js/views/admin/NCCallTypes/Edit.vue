<template>
    <div>
        <h1>Edit Call Type</h1>
        <form @submit.prevent="updateCallType">
            <div>
                <label for="call_type_name">Call Type Name:</label>
                <input type="text" v-model="callType.call_type_name" required />
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="callType.status" required>
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
            callType: {
                call_type_name: '',
                status: 'active',
                created_by: '',
                updated_by: '',
                last_updated_by: '',
            }
        };
    },
    async created() {
        await this.fetchCallType();
    },
    methods: {
        async fetchCallType() {
            try {
                // Ensure the action completes and returns data
                const callTypes = await this.$store.dispatch('fetchCallTypes');
                console.log('Call types available:', callTypes);
                if (Array.isArray(callTypes)) {
                    const callType = callTypes.find(type => type.id === parseInt(this.$route.params.id));
                    if (callType) {
                        this.callType = callType;
                    } else {
                        console.error('Call type not found');
                        this.$router.push('/admin/call-types');
                    }
                } else {
                    console.error('Call types are not an array or not defined');
                }
            } catch (error) {
                console.error('Error fetching call type:', error);
            }
        },
        async updateCallType() {
            try {
                await this.$store.dispatch('updateCallType', { id: this.$route.params.id, ...this.callType });
                this.$router.push('/admin/call-types');
            } catch (error) {
                console.error('Error updating call type:', error);
            }
        }
    }
};
</script>
