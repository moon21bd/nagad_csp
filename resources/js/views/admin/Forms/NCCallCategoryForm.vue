<template>
    <div>
        <form @submit.prevent="submitForm">
            <div>
                <label for="call_type_id">Call Type:</label>
                <select v-model="form.call_type_id" id="call_type_id">
                    <option
                        v-for="type in callTypes"
                        :key="type.id"
                        :value="type.id"
                    >
                        {{ type.call_type_name }}
                    </option>
                </select>
            </div>
            <div>
                <label for="call_category_name">Name:</label>
                <input
                    type="text"
                    v-model="form.call_category_name"
                    id="call_category_name"
                />
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="form.status" id="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">{{ isEdit ? "Update" : "Create" }}</button>
        </form>
    </div>
</template>

<script>
export default {
    name: "CallCategoryForm",
    props: {
        categoryId: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            form: {
                call_type_id: "",
                call_category_name: "",
                status: "active",
            },
        };
    },
    computed: {
        isEdit() {
            return this.categoryId !== null;
        },
        callTypes() {
            return this.$store.state.callTypes;
        },
    },
    created() {
        if (this.isEdit) {
            this.$store
                .dispatch("fetchCallCategory", this.categoryId)
                .then((data) => {
                    this.form = { ...data };
                });
        }
        this.$store.dispatch("fetchCallTypes");
    },
    methods: {
        submitForm() {
            if (this.isEdit) {
                this.$store.dispatch("updateCallCategory", {
                    id: this.categoryId,
                    ...this.form,
                });
            } else {
                this.$store.dispatch("createCallCategory", this.form);
            }
        },
    },
};
</script>
