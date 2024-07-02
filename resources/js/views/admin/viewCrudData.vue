<template>
    <div id="here">
        <vuetable ref="vuetable"
                  :api-mode="false"
                  :fields="columns"
                  :per-page="perPage"
                  :data-manager="dataManager"
                  pagination-path="pagination"
                  @vuetable:pagination-data="onPaginationData"
        >
            <div slot="action">
                <button
                    class="ui small button"
                    @click="onActionClicked('view-item', props.rowData)"
                >
                    <i class="zoom icon"></i>
                </button>
            </div>
        </vuetable>
        <div style="padding-top:10px">
            <vuetable-pagination ref="pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            ></vuetable-pagination>
        </div>
    </div>
</template>

<script>
import Vuetable from 'vuetable-2'
import _ from "lodash";
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";


export default {
    name: 'viewCrudData',
    components: {
        Vuetable,
        VuetablePagination
    },
    data() {
        return {
            apiUrl: '',
            requestedPage: this.$route.params.page ?? '',
            data: [],
            perPage:5,
            columns: [
                { name: 'id', title: 'ID', sortField: 'id' },
                { name: 'call_type_name', title: 'Name', sortField: 'name' },
                { name: 'status', title: 'status', sortField: 'status' },
                { name: 'created_by', title: 'created_by', sortField: 'created_by' },
                { name: 'updated_by', title: 'updated_by', sortField: 'updated_by' },
                { name: 'last_updated_by', title: 'last_updated_by', sortField: 'last_updated_by' },
                'action'
            ],
        }
    },
    watch: {
        data(newVal, oldVal) {
            this.$refs.vuetable.refresh();
        }
    },
    mounted() {
        if (this.requestedPage === 'callType') {
            this.apiUrl += 'get_call_type'
        }
        this.init()
    },
    methods: {
        async init() {
            await this.getData().then(response => {
                this.data = response.data
            })
        },
        onPaginationData(paginationData) {
            console.log('paginate test',paginationData)
            this.$refs.pagination.setPaginationData(paginationData);
        },
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
        },
         dataManager(sortOrder, pagination) {
            if (this.data.length < 1) return;

            let local = [...this.data];

            // sortOrder can be empty, so we have to check for that as well
            if (sortOrder.length > 0) {
                console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
                local = _.orderBy(
                    local,
                    sortOrder[0].sortField,
                    sortOrder[0].direction
                );
            }

            pagination = this.$refs.vuetable.makePagination(
                local.length,
                this.perPage
            );
            console.log('pagination:', pagination)
            let from = pagination.from - 1;
            let to = from + this.perPage;
            const slicedData = _.slice(local, from, to);
            return {
                pagination: pagination,
                data: slicedData
            };
        },
        onActionClicked(action, data) {
            console.log("slot actions: on-click", data.name);
        },
        getData() {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.apiUrl)
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
                    .finally(() => {
                    })
            })
        }
    }
}
</script>
