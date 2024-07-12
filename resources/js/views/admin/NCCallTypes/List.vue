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
import axios from "../../../axios";

export default {
    data() {
        return {
            callTypes: []
        };
    },
    methods: {
        async deleteCallType(id) {
            try {
                if (confirm("Are you sure you want to delete this call Type?")) {
                    await axios.delete(`/call-types/${id}`);
                    this.fetchCallTypes()
                }

            } catch (error) {
                console.error("Error deleting call type:", error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
                console.log('response', response.data)
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        }
    },
    mounted() {
        this.fetchCallTypes()
    }
};
</script>

