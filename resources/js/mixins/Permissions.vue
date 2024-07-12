<script>
export default {
    methods: {
        $can(permissionName) {
            console.log('Checking permission:', permissionName);
            console.log('User permissions:', this.$store.state.user);
            return this.$store.state.auth.user.cando.indexOf(permissionName) !== -1;
        },
    },
};

</script>
