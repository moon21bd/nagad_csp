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
import axios from "../../../axios";

export default {
    data() {
        return {
            groups: []
        };
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async deleteGroup(groupId) {
            try {
                if (confirm('Are you sure you want to delete this group?')) {
                    await axios.delete(`/groups/${groupId}`);
                    this.fetchGroups()
                }

            } catch (error) {
                console.error("Error deleting group:", error);
            }
        },

    },
    mounted() {
        this.fetchGroups()
    }

};
</script>
