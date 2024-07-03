<template>
    <div>
        <h1>Call Types</h1>
        <router-link to="/admin/call-types/create">Create New Call Type</router-link>
        <ul>
            <li v-for="callType in callTypes" :key="callType.id">
                {{ callType.call_type_name }}
                <router-link :to="{ name: 'call-types-edit', params: { id: callType.id } }">Edit</router-link>
                <button @click="deleteCallType(callType.id)">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    computed: {
        callTypes() {
            return this.$store.getters.callTypes;
        }
    },
    created() {
        this.$store.dispatch('fetchCallTypes');
    },
    methods: {
        async deleteCallType(id) {
            if (confirm('Are you sure you want to delete this call type?')) {
                try {
                    await this.$store.dispatch('deleteCallType', id);
                } catch (error) {
                    console.error('Error deleting call type:', error);
                }
            }
        }
    }
};
</script>

