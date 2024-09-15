<template>
    <button type="submit" class="btn btn-block" :disabled="isLoading">
        <span v-if="!isLoading">{{ text }}</span>
        <div class="spinner-border text-light" role="status" v-if="isLoading">
            <span class="sr-only">Loading...</span>
            <!-- <img src="/images/loader.gif" alt="" /> -->
        </div>
    </button>
</template>
<script>
export default {
    name: "LoadingButton",
    props: {
        text: String,
        isLoading: Boolean,
    },
};
</script>
