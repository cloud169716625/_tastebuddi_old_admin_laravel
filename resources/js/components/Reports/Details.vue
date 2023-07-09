<template>
    <div id="item-info-content">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/admin/reports/">
                        Reports
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a :href="getReportMetaByReportType()['url']">
                        {{ getReportMetaByReportType()['model'] }}
                    </a>
                </li>
                <li class="breadcrumb-item active">{{ getReportableTitle() }}</li>
            </ol>
            <div style="float: right">
                <el-button type="danger" :disabled="!!reportDetails.reportable.deleted_at || !!reportDetails.reportable.disabled_at" v-if="reportDetails" @click="disableModel">
                    <i class="el-icon-warning-outline"></i> Disable
                </el-button>
            </div>
        </div>
        <hr />
        <div class="content">
            <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span v-if="reportDetails">Report Details</span>
                <span v-else></span>
                <span style="float: right;">{{ getReportMetaByReportType()['model'] }}</span>
            </div>
            <div class="demo-image__preview" v-if="reportDetails">
                <el-form
                    label-position="left"
                    label-width="250px"
                    style="mt-3"
                >
                    <el-form-item label="Reported">
                    <el-input readonly :value="getReportableTitle()"/>
                    </el-form-item>
                    <el-form-item v-if="isUserReport()" label="Reported Email">
                        <el-input readonly :value="reportDetails.reportable.email"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="Recommended By">
                        <el-input readonly :value="reportDetails.reportable.user.full_name"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="Location Name">
                        <el-input readonly :value="reportDetails.reportable.location.location"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="Address">
                        <el-input readonly :value="reportDetails.reportable.location.address"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="City">
                        <el-input readonly :value="reportDetails.reportable.location.city.name"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="Country">
                        <el-input readonly :value="reportDetails.reportable.location.city.country.country_name"/>
                    </el-form-item>
                    <el-form-item v-if="isRecommendationReport()" label="Recommended Price">
                        <el-input readonly :value="reportDetails.reportable.recommended_price"/>
                    </el-form-item>
                    <el-form-item v-if="isItemReport()" label="City">
                        <el-input readonly :value="reportDetails.reportable.city.name"/>
                    </el-form-item>
                    <el-form-item v-if="isItemReport()" label="Country">
                        <el-input readonly :value="reportDetails.reportable.city.country.country_name"/>
                    </el-form-item>
                    <el-form-item label="Reported By">
                    <el-input readonly v-model="reportDetails.reported_by.full_name"/>
                    </el-form-item>
                    <el-form-item label="Reported At">
                    <el-input readonly v-model="reportDetails.created_at"/>
                    </el-form-item>
                    <el-form-item label="Description">
                    <el-input readonly v-model="reportDetails.description"/>
                    </el-form-item>
                    <el-form-item label="Reason">
                    <el-input readonly v-model="reportDetails.reason.label"/>
                    </el-form-item>
                </el-form>
                <h5 class="mt-5">Attachments</h5>
                <div v-if="reportDetails.attachments.length > 0">
                    <el-image
                        v-for="(attachment, key) in reportDetails.attachments"
                        :key="key"
                        style="width: 10%; height: 10%"
                        class="pl-3"
                        :src="attachment.url"
                        :preview-src-list="getAttachmentURLs()"
                        :fit="fit"
                    >
                    </el-image>
                </div>
                <div v-else>
                    <i>No attachments found.</i>
                </div>
            </div>
            </el-card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            reportDetails: null,
            endpoints: {
                reports: '/ajax/reports',
            },
            reportTypeMeta: {
                items: {
                    model: 'Items',
                    url: '/admin/items',
                    field: 'item_name'
                },
                users: {
                    model: 'Users',
                    url: '/admin/users',
                    field: 'full_name'
                },
                recommendations: {
                    model: 'Recommendations',
                    url: '#',
                    field: 'item.item_name'
                }
            },
            fit: 'fill',
            selectedModel: null
        }
    },
    methods: {
        async getData() {
            const id = new URL(location.href).toString().split('/').slice(-1).pop()

            await axios.get(this.endpoints.reports.concat(`/${id}`))
                .then(response => {
                    this.reportDetails = response.data
                })
                .catch(err => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                })
        },

        isUserReport() {
            return this.getReportMetaByReportType()['model'] == 'Users'
        },

        isRecommendationReport() {
            return this.getReportMetaByReportType()['model'] == 'Recommendations'
        },

        isItemReport() {
            return this.getReportMetaByReportType()['model'] == 'Items'
        },

        getReportMetaByReportType() {
            return this.reportDetails && this.reportDetails.report_type
                    ? {
                        ...this.reportTypeMeta[this.reportDetails.report_type]
                    }
                    : {model: 'Loading . . .', url: '', field: ''}
        },

        getReportableTitle() {
            return this.reportDetails && this.reportDetails.report_type
                    ? this.getReportMetaByReportType()['field'].toString().split('.').reduce(function (curr, prev) {
                        return curr[prev]
                    }, JSON.parse(JSON.stringify(this.reportDetails.reportable)))
                    : 'Loading . . .'
        },

        getAttachmentURLs() {
            return this.reportDetails && this.reportDetails.attachments
                    ? this.reportDetails.attachments.map(attachment => attachment.url)
                    : []
        },

        disableModel() {
            const selectedModel = this.getReportMetaByReportType()['model'].slice(0, -1)
            let message = ''

            if (selectedModel == 'User') {
                message = `Are you sure you want to disable this ${selectedModel}?`
            } else {
                message = `Are you sure you want to disable this ${selectedModel}? You can no longer reactivate it after this action.`
            }

            this.$confirm(message, 'Warning', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios.delete(this.endpoints.reports.concat(`/${this.reportDetails.id}/disable`))
                    .then(_ => {
                        this.getData()
                        this.$message({
                            type: 'success',
                            message: `${selectedModel} disabled!`
                        });
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: err && err.response.data.message ? err.response.data.message : 'Something went wrong'
                        })
                    })
            })
        },
    },
    mounted() {
        this.getData()
    }
};
</script>

<style scoped>
    .el-form-item {
        margin-bottom: 2px;
    }
</style>
