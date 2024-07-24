<template>
    <div>
        <h1>Manage Roles for {{ group.name }}</h1>
        <div>
            <label for="roles">Roles</label>
            <select id="roles" v-model="selectedRoles" multiple>
                <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.name }}
                </option>
            </select>
            <button @click="assignRoles">Assign Roles</button>
        </div>
        <ul>
            <li v-for="role in group.roles" :key="role.id">
                {{ role.name }}
                <button @click="removeRole(role.id)">Remove</button>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from "../../../axios";

export default {
    data() {
        return {
            id: this.$route.params.id,
            group: {
                roles: [],
            },
            roles: [],
            selectedRoles: [],
        };
    },
    methods: {
        fetchGroup() {
            axios.get(`/groups/${this.id}`).then((response) => {
                this.group = response.data;
                this.selectedRoles = this.group.roles.map((role) => role.name);
            });
        },
        fetchRoles() {
            axios.get("/roles").then((response) => {
                this.roles = response.data;
            });
        },
        assignRoles() {
            axios
                .post(`/groups/${this.id}/roles`, { roles: this.selectedRoles })
                .then(() => {
                    this.fetchGroup();
                    this.selectedRoles = [];
                    this.fetchRoles();
                })
                .catch((error) => {
                    console.error("Error assigning roles:", error);
                });
        },
        removeRole(roleId) {
            axios
                .delete(`/groups/${this.id}/roles/${roleId}`)
                .then(() => {
                    this.fetchGroup();
                })
                .catch((error) => {
                    console.error("Error removing role:", error);
                });
        },
    },
    created() {
        this.fetchGroup();
        this.fetchRoles();
    },
};
</script>
