<template>
    <div id="item-list-wrapper">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    <a href="/admin/reports/">
                        Reports
                    </a>

                </li>

            </ol>

        </div>

        <div class="content p-20">
            <loading-screen ref="loadingScreen">

            <el-form :inline="true" class="demo-form-inline">
                <el-form-item label="Report by">
                    <el-input v-model="filters.search" placeholder="Report by" @input="doFilter" clearable />
                </el-form-item>

                <el-form-item label="Reason">
                    <el-select v-model="filters.reason_id" placeholder="Reason" @change="doFilter" clearable>
                        <el-option
                            v-for="(value, key) in reportCategories"
                            :key="key"
                            :label="getCategoryLabel(value.id)"
                            :value="value.id"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="Type">
                    <el-select v-model="filters.reportable_type" placeholder="Type" @change="doFilter" clearable>
                        <el-option
                            v-for="(value, key) in reportTypes"
                            :key="key"
                            :label="value.text"
                            :value="getReportableType(value.value)"
                        />
                    </el-select>
                </el-form-item>
            </el-form>

                <table class="table table-borderless">

                    <thead>
                    <tr class="header">
                        <th @click="sort('reporter')">
                            <el-link type="primary">Reported By</el-link>
                            <i
                                v-if="sorts.reporter != null"
                                class="fa"
                                :class="{
                                'fa-caret-up' : sorts.reporter,
                                    'fa-caret-down' : !sorts.reporter }"
                                >
                            </i>
                        </th>
                        <th>Reported</th>
                        <th @click="sort('type')">
                            <el-link type="primary">Type</el-link>
                            <i
                                v-if="sorts.type != null"
                                class="fa"
                                :class="{
                                'fa-caret-up' : sorts.type,
                                    'fa-caret-down' : !sorts.type }"
                                >
                            </i>
                        </th>
                        <th @click="sort('reason')">
                            <el-link type="primary">Reason</el-link>
                            <i
                                v-if="sorts.reason != null"
                                class="fa"
                                :class="{
                                'fa-caret-up' : sorts.reason,
                                    'fa-caret-down' : !sorts.reason }"
                                >
                            </i>
                        </th>
                        <th @click="sort('created_at')">
                            <el-link type="primary">Created At</el-link>
                            <i
                                v-if="sorts.created_at != null"
                                class="fa"
                                :class="{
                                'fa-caret-up' : sorts.created_at,
                                    'fa-caret-down' : !sorts.created_at }"
                                >
                            </i>
                        </th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr
                        v-for="( value , key ) in items"
                        :key="key"
                        class="pointer">

                        <td @click="goToItem( value )" class="align-middle">
                            {{ `${value.reported_by.first_name} ${value.reported_by.last_name}` }}
                        </td>

                        <td @click="goToItem( value )" class="align-middle">
                            {{ getReportableName(value) }}
                        </td>

                        <td @click="goToItem( value )" class="align-middle">
                            {{ getReportType(value.report_type) }}
                        </td>

                        <td @click="goToItem( value )" class="align-middle">
                            {{ value.reason.label }}
                        </td>

                        <td @click="goToItem( value )" class="align-middle">
                            {{ value.created_at }}
                        </td>


                        <td class="light-dark align-middle">
                            <el-dropdown trigger="click" @command="deleteReport">
                                <span class="el-dropdown-link">
                                    <i class="el-icon-more"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="value.id"><i class="icon-trash"></i> Delete</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </loading-screen>

            <el-pagination
                :page-size="this.options.perPage"
                layout="prev, pager, next"
                :total="this.itemsMeta.total"
                @current-change="paginate"
                :hide-on-single-page="true"
            />
        </div>

        <div id="deleteItemModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>

                    <div class="modal-body text-center">
                        <i class="icon-warning icon-2x" style="color: orange;" ></i> Are you sure you want to delete this report?
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="triggerDeleteReport">
                            Delete
                        </button>
                        <button
                            type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import LoadingScreen from 'vue-loading-screen';
import Pagination from 'laravel-vue-pagination';
import debounce from 'lodash/debounce'

export default{
    name: 'item-list',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen,
        Pagination,
    },
    data() {
        return {
            items: [],
            itemsMeta: [],
            reportCategories: [],
            reportTypes: [
                {
                    value: 'recommendations',
                    text: 'Recommendations',
                    model: 'App\\Models\\Items\\Recommendation',
                },
                {
                    value: 'users',
                    text: 'Users',
                    model: 'App\\Models\\Users\\Users',
                },
                {
                    value: 'items',
                    text: 'Items',
                    model: 'App\\Models\\Items\\Item',
                },
            ],
            endpoints: {
                reports: '/ajax/reports',
                reportCategories: '/ajax/reports/categories'
            },
            options: {
                page: 1,
                perPage: 10
            },
            filters: {
                search: null,
                type: null,
                reason_id: null
            },
            sorts: {
                created_at: false
            },
            selectedId: null
        }
    },
    methods: {
        async getData() {
            const filters = Object.keys(this.filters).reduce((prev, curr) => {
                prev[`filter[${curr}]`] = this.filters[curr]
                return prev
            }, {})

            const sorts = Object.keys(this.sorts).reduce((prev, curr, index) => {
                prev[index] = this.sorts[curr] ? curr : `-${curr}`
                return prev
            }, []).join(',')

            await axios.get(this.endpoints.reports, {
                params: {
                    ...this.options,
                    ...filters,
                    sort: sorts
                }
            }).then(result => {
                this.items = result.data.data
                this.itemsMeta = result.data.meta
            })
        },

        async getReportCategories() {
            await axios.get(this.endpoints.reportCategories, {
                params: {
                    perPage: 999
                }
            }).then(result => {
                this.reportCategories = result.data.data
            })
        },

        getReportType(value) {
            return this.reportTypes.find(type => type.value === value).text
        },

        getCategoryLabel(value) {
            const category = this.reportCategories.find(category => category.id === value)

            return category.type ? `${category.label} (${this.getReportType(category.type)})` : category.label
        },

        getReportableType(value) {
            return this.reportTypes.find(type => type.value === value).model
        },

        getReportableName(value) {
            if (value.reportable == null) return 'Reported entity is not existing'

            if (value.report_type == 'users') {
                return value.reportable.full_name
            }

            if (value.report_type == 'recommendations') {
                return value.reportable.item.item_name
            }

            if (value.report_type == 'items') {
                return value.reportable.item_name
            }

            return null
        },

        doFilter: debounce(function() {
            this.options.page = 1
            this.getData()
        }, 600),

        paginate(page) {
            this.options.page = page
        },

        sort(field) {
            if (!this.sorts[field]) {
                this.sorts = []
            }

            this.sorts[field] = !this.sorts[field]

            this.doFilter()
        },

        deleteReport(reportId) {
            this.selectedId = reportId
            $('#deleteItemModal').modal();
        },

        async triggerDeleteReport() {
            await axios.delete(this.endpoints.reports.concat(`/${this.selectedId}`),)
            .then(_ => {
                this.$notify.error({
                    title: 'Deleted',
                    message: 'Report deleted!',
                });
                this.getData()
            }).catch(response => {
                this.$notify.error({
                    title: 'Error',
                    message: 'Something went wrong',
                });
            }).finally(_ => {
                $('#deleteItemModal').modal( 'toggle' );
            })
        },

        goToItem(value) {
            if (value.reportable == null) return

            location.href =`/admin/reports/${value.id}`;
        },
    },
    mounted() {
        this.getReportCategories()
        this.getData()
    },
    watch: {
        options: {
            handler() {
                this.getData()
            },
            deep: true
        }
    }
}
</script>
