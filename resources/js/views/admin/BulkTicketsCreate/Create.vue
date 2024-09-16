<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'bulk-tickets-create-list' }"
            >
                <i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Bulk Ticket</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="Loading..." />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="handleSubmit">
                            <div class="form-row">
                                <div class="col-md-6 form-group uploads">
                                    <label class="control-label">
                                        Excel File<sup>*</sup>
                                    </label>
                                    <input
                                        type="file"
                                        name="excel_file"
                                        v-validate="'required|excel'"
                                        @change="handleFileUpload"
                                        accept=".xls,.xlsx"
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('excel_file')"
                                    >
                                        {{ errors.first("excel_file") }}
                                    </small>
                                </div>
                            </div>

                            <div class="">
                                <a
                                    class="btn btn-outline-secondary"
                                    href="/templates/bulk_ticket_create_sample.xlsx"
                                    download
                                    target="_blank"
                                >
                                    <i class="icon-download"></i>
                                    Sample Template
                                </a>
                            </div>
                            <br />
                            <button class="btn btn-site" type="submit">
                                Upload
                            </button>
                        </form>

                        <ul class="list-group" v-if="formErrors.length">
                            <li
                                class="list-group-item list-group-item-danger"
                                v-for="error in formErrors"
                                :key="error"
                            >
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { Validator } from "vee-validate";

// Extend vee-validate with a custom rule for Excel file validation
Validator.extend("excel", {
    getMessage: (field) => `The ${field} must be an Excel file (.xls, .xlsx)`,
    validate: (value) => {
        const allowedTypes = [
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ];

        if (!value || !value.length) {
            return false;
        }

        const fileType = value[0].type;
        return allowedTypes.includes(fileType);
    },
});

export default {
    data() {
        return {
            isLoading: false,
            formData: {
                excel_file: null, // Initialize formData with excel_file property
            },
            formErrors: [],
        };
    },
    methods: {
        handleFileUpload(event) {
            this.formData.excel_file = event.target.files[0];
        },
        async handleSubmit() {
            this.formErrors = []; // Reset form errors
            this.isLoading = true;

            this.$validator.validateAll().then(async (result) => {
                if (result) {
                    // Proceed with file upload
                    this.uploadFile();
                } else {
                    this.isLoading = false;
                }
            });
        },
        uploadFile() {
            const formData = new FormData();
            formData.append("excel_file", this.formData.excel_file);

            axios
                .post("/bulk-ticket-create", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then((response) => {
                    this.isLoading = false;
                    this.formData.excel_file = null;

                    Vue.prototype.$showToast("File uploaded successfully", {
                        type: "success",
                    });
                    this.$router.push({ name: "bulk-tickets-create-list" });
                })
                .catch((error) => {
                    this.isLoading = false;
                    if (error.response && error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    } else {
                        this.formErrors.push(
                            "Upload failed. Please try again later."
                        );
                    }
                });
        },
    },
};
</script>
