<template>
    <div>
        <input type="file" @change="handleFileUpload" />
        <button @click="uploadFile">Upload Status Updates</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            file: null,
        };
    },
    methods: {
        handleFileUpload(event) {
            this.file = event.target.files[0];
        },
        uploadFile() {
            const formData = new FormData();
            formData.append("excel_file", this.file);

            this.$http
                .post("/bulk-ticket-status-update", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then((response) => {
                    console.log("File uploaded successfully");
                })
                .catch((error) => {
                    console.error("Upload failed", error);
                });
        },
    },
};
</script>
