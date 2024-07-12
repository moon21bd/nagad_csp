<template>
    <div>
        <h1>Create Call Type</h1>
        <form @submit.prevent="createCallType">
            <div>
                <label for="call_type_name">Call Type Name:</label>
                <input type="text" v-model="callType.call_type_name" required/>
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="callType.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Create</button>
        </form>
    </div>
</template>

<script>
import axios from "../../../axios";

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
    methods: {
        async createCallType() {
            try {
                await axios.post("/call-types", this.callType);
                this.callType = {};
                this.$router.push({name: "call-types-index"})
            } catch (error) {
                console.error("Error creating call type:", error);
            }
        }
    },

};
</script>
