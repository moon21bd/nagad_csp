<template>
    <div>
        <h2>Groups List</h2>
        <ul>
            <li v-for="group in groups" :key="group.id">
                {{ group.name }}
                <router-link :to="{ name: 'editGroup', params: { id: group.id }}">Edit</router-link>
                <button @click="deleteGroup(group.id)">Delete</button>
            </li>
        </ul>
        <router-link :to="{ name: 'createGroup' }">Create New Group</router-link>
    </div>
</template>

<script>
export default {
    computed: {
        groups() {
            return this.$store.state.groups;
        }
    },
    methods: {
        async deleteGroup(groupId) {
            if (confirm('Are you sure you want to delete this group?')) {
                try {
                    await this.$store.dispatch('deleteGroup', groupId);
                    console.log('Group deleted successfully.');
                } catch (error) {
                    console.error('Error deleting group:', error);
                }
            }
        }
    },
    created() {
        this.$store.dispatch('fetchGroups');
    }
};
</script>
