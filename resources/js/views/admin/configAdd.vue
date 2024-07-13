<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type Id</label>
                <input type="text" v-model="callTypeId" class="form-control" id="name"
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Call Category Id</label>
                <input type="text" v-model="callCategoryId" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Call Sub Category Id</label>
                <input type="text" v-model="callSubCategoryId" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Input Filed Name</label>
                <input type="text" v-model="inputFiledName" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Input Type</label>
                <select class="form-control" v-model="inputType" id="">
                    <option value="integer">Number</option>
                    <option value="varchar">String</option>
                    <option value="select">Select/Option</option>
                    <option value="text">Text</option>
                    <option value="datetime">DateTime</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Input Value</label>
                <input type="text" v-model="inputValue" class="form-control" id="" placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Input Validation</label>
                <input type="text" v-model="inputValidation" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Status</label>
                <select class="form-control" v-model="statusValue" id="status">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button @click.prevent="submit" type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'configAdd',
    data() {
        return {
            name: '',
            statusValue: '',
            actionUrl: 'store_config',
            apiUrl:'',
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            inputValue: '',
            inputType: '',
            inputFiledName: '',
            inputValidation: 'required,'
        }
    },
    mounted() {
        // if (this.requestedPage === 'callType') {
        //     this.actionUrl += 'store_call_type'
        // }
    },
    methods: {
        async init() {

        },
        storeData() {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.actionUrl, {
                        inputFiledName: this.inputFiledName,
                        callTypeId: this.callTypeId,
                        callCategoryId: this.callCategoryId,
                        callSubCategoryId: this.callSubCategoryId,
                        inputValue: this.inputValue,
                        inputType: this.inputType,
                        inputValidation: this.inputValidation,
                        statusValue: this.statusValue
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
                this.inputFiledName = ''
                this.callTypeId = null
                this.callCategoryId =null
                this.callSubCategoryId =null
                this.inputValue = ''
                this.inputType = ''
                this.inputValidation = 'required,'
                this.statusValue = ''
            })
        }
    }
}
</script>
