<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type Name</label>
                <input type="text" v-model="name" class="form-control" id="name" placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Status</label>
                <select class="form-control" v-model="statusValue" id="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button @click.prevent="submit" type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'addData',
    data() {
        return {
            name: '',
            statusValue: '',
            actionUrl: '',
            apiUrl: 'content/',
            requestedPage: this.$route.params.page ?? ''
        }
    },
    mounted() {
        if (this.requestedPage === 'callType') {
            this.actionUrl += 'store_call_type'
        }
    },
    methods: {
        async init() {

        },
        storeData() {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.actionUrl, {
                        name: this.name,
                        status: this.statusValue
                    })
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
                    .finally(() => {
                    })
            })
        },
        async submit() {
            await this.storeData().then(response => {
                this.name = ''
                this.statusValue = ''
            })
        }
    }
}
</script>
