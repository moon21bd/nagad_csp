<template>
    <div class="card">
        <div class="card-body">
            <div class="no-data py-4">
                <img src="/images/no-data.svg" alt="Permission Denied" />
                <h3>Permission Denied</h3>
                <p>
                    You do not have the necessary permissions to access this
                    page.
                </p>
                <router-link to="/" class="btn btn-site"
                    >Go to Home</router-link
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PermissionDenied",
};
</script>
