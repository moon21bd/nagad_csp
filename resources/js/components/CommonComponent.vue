<template>
    <!-- This component doesn't render anything itself -->
    <div></div>
</template>

<script>
export default {
    name: "CommonComponent",
    methods: {
        commonMethod() {
            console.log("This method is available in all components");
        },
    },
};
</script>
