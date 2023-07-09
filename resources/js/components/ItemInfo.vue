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
                    <a href="/admin/items/">
                        Items
                    </a>
                </li>

                <li class="breadcrumb-item active">{{ item.item_name }}</li>

            </ol>

        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">
                    <hgroup>
                        <h2>{{ item.item_name }}</h2>
                    </hgroup>
                </div>

                <!-- card body -->
                <div class="text item">

                    <loading-screen ref="loadingScreen">

                        <el-form
                            :model="item"
                            ref="item"
                            :rules="rules"
                            label-width="200px"
                            class="pr-5 pt-5">

                            <el-form-item label="Photo">

                                <div class="col-4 px-0">

                                    <img
                                        :src="thumbnailSrc(item)"
                                        class="img-fluid border img-thumbnail"/>

                                    <el-button
                                        v-if="!addPhoto"
                                        type="success"
                                        icon="el-icon-circle-plus-outline"
                                        size="mini"
                                        @click="addPhoto = !addPhoto">
                                        Edit Photo
                                    </el-button>

                                    <el-button
                                        v-if="addPhoto"
                                        type="danger"
                                        icon="el-icon-circle-close"
                                        size="mini"
                                        @click="addPhoto = !addPhoto">
                                        Close
                                    </el-button>

                                </div>

                                <div v-if="addPhoto">

                                    <vue-dropzone
                                        ref="myVueDropzone"
                                        id="dropzone"
                                        :options="itemPhotoDropzoneOptions"
                                        v-on:vdropzone-sending="sendingEvent"
                                        v-on:vdropzone-success="successUpload"
                                        class="col-lg-4">
                                    </vue-dropzone>

                                </div>

                            </el-form-item>

                            <el-form-item label="Name" prop="item_name">

                                <el-input v-model="item.item_name"></el-input>

                            </el-form-item>

                            <el-form-item label="Category" prop="category_id">

                                <el-select
                                    v-model="item.category_id"
                                    placeholder="Select a Category"
                                    loading-text="loading categories..."
                                    clearable
                                    filterable>

                                    <el-option
                                        v-for="item in categories"
                                        :key="item.category_id"
                                        :label="item.category_name"
                                        :value="item.category_id">
                                    </el-option>

                                </el-select>

                            </el-form-item>

                            <el-form-item label="Watchers">

                                <el-input v-model="watchers" readonly></el-input>

                            </el-form-item>

                            <el-form-item label="Contributors">

                                <el-input v-model="contributors" readonly></el-input>

                            </el-form-item>

                            <el-form-item label="City" prop="city_id">

                                <el-select
                                    v-model="item.city_id"
                                    placeholder="Select a City"
                                    loading-text="loading cities..."
                                    clearable
                                    filterable
                                    disabled>

                                    <el-option
                                        v-for="item in cities"
                                        :key="item.city_id"
                                        :label="item.city_name"
                                        :value="item.city_id">
                                    </el-option>

                                </el-select>

                            </el-form-item>

                            <el-form-item label="Custom Lowest Price" prop="custom_price_low">
                                <el-col>
                                    <el-input v-model="item.custom_price_low" placeholder="Custom Price Low"></el-input>
                                </el-col>
                            </el-form-item>

                            <el-form-item label="Custom Highest Price" prop="custom_price_high">
                                <el-col>
                                    <el-input v-model="item.custom_price_high" placeholder="Custom Price High"></el-input>
                                </el-col>
                            </el-form-item>

                            <el-form-item label="Custom Average Price" prop="custom_price_average">
                                <el-col>
                                    <el-input v-model="item.custom_price_average" placeholder="Custom Price Average"></el-input>
                                </el-col>
                            </el-form-item>

                            <el-form-item label="Details">
                                <el-card>
                                    <div slot="header">
                                        <el-button
                                            size="small"
                                            type="primary"
                                            @click="showAddItemDetailDialog"
                                            >Add Details</el-button>
                                        <div class="float-right">
                                            <div class="row">
                                                <div class="col">
                                                    <el-input placeholder="Recommendation By" v-model="filters.recommendedBy" clearable></el-input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <el-table :data="details" border style="width: 100%" :row-class-name="statusRow">
                                        <el-table-column
                                            prop="item_detail_id"
                                            label="ID"
                                            width="100"
                                            sortable>
                                        </el-table-column>

                                        <el-table-column
                                                prop="location"
                                                label="Location" sortable>
                                        </el-table-column>

                                        <el-table-column
                                            label="Price" sortable>

                                            <template slot-scope="scope">
                                                {{ formatPrice(scope.row.price) }}
                                            </template>
                                        </el-table-column>


                                        <el-table-column label="Type">

                                            <template slot-scope="scope">
                                                {{ getItemDetailType(scope.row) }}
                                            </template>

                                        </el-table-column>

                                        <el-table-column prop="item_detail_id" label width="250" align="center">

                                            <template slot-scope="scope">
                                                <el-button-group>
                                                    <el-button size="mini" type="warning" @click="updateItemDetailStatus(scope.row, 'rejected')" v-if="scope.row.recommendation" :disabled="scope.row.status === 'rejected'">Reject</el-button>
                                                    <el-button size="mini" type="success" @click="updateItemDetailStatus(scope.row, 'approved')" v-if="scope.row.recommendation" :disabled="scope.row.status === 'approved'">Approve</el-button>
                                                    <el-button
                                                        type="danger"
                                                        @click.stop.prevent="deleteDetail(scope.row.item_detail_id)"
                                                        size="mini">
                                                        Delete
                                                    </el-button>
                                                </el-button-group>
                                            </template>

                                        </el-table-column>

                                    </el-table>
                                </el-card>

                            </el-form-item>

                            <el-form-item class="mt-2">

                                <el-button type="primary" @click="checkfields('item')">
                                    Save
                                </el-button>

                            </el-form-item>

                        </el-form>

                    </loading-screen>

                </div>

            </el-card>
        </div>

        <el-dialog
            title="Add Item Details"
            :visible.sync="dialogVisible"
            width="30%"
            :before-close="handleClose">

            <el-form
                :rules="priceRules"
                :model="detail"
                ref="detail">

                <el-form-item label="Location" prop="location_id">

                    <el-select
                        v-model="detail.location_id"
                        placeholder="Select a Location"
                        loading-text="loading locations..."
                        clearable
                        filterable>

                        <el-option
                            v-for="item in locations"
                            :key="item.location_id"
                            :label="item.location"
                            :value="item.location_id">
                        </el-option>

                    </el-select>

                </el-form-item>

                <el-form-item label="Price" prop="price">

                    <el-input-number
                        v-model="detail.price"
                        clearable
                        :controls=false
                        :precision="2">
                    </el-input-number>

                </el-form-item>

            </el-form>

            <span slot="footer" class="dialog-footer">

                <el-button @click="dialogVisible = false">
                    Cancel
                </el-button>
                <el-button
                    type="primary"
                    @click="checkfields('detail')">
                    Save
                </el-button>

            </span>

        </el-dialog>

    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import LoadingScreen from 'vue-loading-screen';

export default {
    name: 'item-info',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen,
    },
    data() {
        return {
            item: {
                item_id: null,
                item_name: '',
                city_id: null,
                category_id: null,
                image_url: '',
                custom_price_low: null,
                custom_price_high: null
            },
            detail: {
                price: null,
                location: '',
                location_id: null,
                lat_coordinate: null,
                lng_coordinate: null,
                item_details_id: null,
                item_id: null,
            },
            watchers: null,
            contributors: null,
            categories: [],
            cities: [],
            details: [],
            locations: [],
            dialogVisible: false,
            endpoints: {
                item: '/ajax/item',
                items: '/ajax/items/',
                getCities: '/ajax/item/cities',
                getCategories: '/ajax/item/categories',
                locations: '/ajax/item/locations',
                details: '/ajax/item/details',
                savePrice: '/ajax/item/price',
            },
            selectedCountry: null,
            selectedCategory: null,
            addPhoto: false,
            itemPhotoDropzoneOptions: {
                url: '/ajax/item/item_photo/upload',
                thumbnailWidth: 150,
                maxFilesize: 2,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dictDefaultMessage:
                    '<i class="icon-cloud icon-2x"></i><br><small>Click to upload photo.</small>',
                maxFiles: 1,
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
            },
            priceRules: {
                'detail.price': [{
                    required: true,
                    message: 'Price is required',
                    trigger: 'blur',
                }]
            },
            itemDetailTypes: [
                { value: 'system', label: 'System' },
                { value: 'verified-business-item', label: 'System - Verified Business Item' },
                { value: 'recommendation', label: 'Recommendation' },
            ],
            itemDetailType: null,
            filters: {
                recommendedBy: null
            }
        }
    },
    computed: {
        rules() {
            return {
                city_name: [{
                    required: true,
                    message: 'City Name is required',
                    trigger: 'blur',
                }, {
                    max: 150,
                    message: 'Length is up to 150 characters only',
                    trigger: 'blur',
                }],
                city_code: [{
                    required: true,
                    message: 'City Code is required',
                    trigger: 'blur',
                }, {
                    max: 10,
                    message: 'Length is up to 10 characters only',
                    trigger: 'blur',
                }],
                city_id: [{
                    required: true,
                    message: 'City is required',
                    trigger: 'change',
                }],
                category_id: [{
                    required: true,
                    message: 'Category is required',
                    trigger: 'change',
                }],
                custom_price_low: [
                    {
                        required: this.item.custom_price_high != null && this.item.custom_price_high != '',
                        message: 'Custom Lowest Price is required when Custom Highest Price is present.',
                        trigger: 'change'
                    },
                    {
                        required: this.item.custom_price_average != null && this.item.custom_price_average != '',
                        message: 'Custom Lowest Price is required when Custom Average Price is present.',
                        trigger: 'change'
                    }
                ],
                custom_price_high: [
                    {
                        required: this.item.custom_price_low != null && this.item.custom_price_low != '',
                        message: 'Custom Highest Price is required when Custom Lowest Price is present.',
                        trigger: 'change'
                    },
                    {
                        required: this.item.custom_price_average != null && this.item.custom_price_average != '',
                        message: 'Custom Highest Price is required when Custom Average Price is present.',
                        trigger: 'change'
                    }
                ],
                custom_price_average: [
                    {
                        required: (this.item.custom_price_low != null && this.item.custom_price_low != '')
                                  || (this.item.custom_price_high != null && this.item.custom_price_high != ''),
                        message: 'Custom Average Price is required when Custom Lowest Price and Custom Highest Price are present.',
                        trigger: 'change'
                    }
                ]
            }
        }
    },
    methods: {
        showAddItemDetailDialog(){
            this.dialogVisible = true;
            this.loadLocations();
        },
        async loadItem() {
            await axios
                .get(this.endpoints.item, {
                    params: { item_id: this.item.item_id }
                })
                .then(result => {
                    let data = result.data.data;
                    this.item = {
                        ...data.item,
                        city_id: data.item.city.id
                    };
                    delete this.item['image_url']
                    this.watchers = data.watchers;
                    this.contributors = data.contributors;

                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async loadLocations() {
            this.locations = [];
            await axios
                .get(this.endpoints.locations,
                    {
                        params: {
                            city_id: this.item.city_id
                        }
                    })
                .then(result => {
                    let data = result.data.data;
                    this.locations = data.locations;
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async loadDetails() {
            await axios
                .get(this.endpoints.details, {
                    params: {
                        item_id: this.item.item_id,
                        name: this.filters.recommendedBy
                    }
                })
                .then(result => {
                    let data = result.data.data;
                    this.details = data.details;
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async loadCities(city_id) {
            await axios
                .get(this.endpoints.getCities)
                .then(result => {
                    let response = result.data;

                    if (response.success) {
                        this.cities = response.data.cities;
                    } else {
                        this.$notify.error({
                            title: 'Error',
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async loadCategories() {
            await axios
                .get(this.endpoints.getCategories)
                .then(result => {
                    let response = result.data;

                    if (response.success) {
                        this.categories = response.data.categories;
                    } else {
                        this.$notify.error({
                            title: 'Error',
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async saveItem() {
            const {city, image, ...rest} = this.item

            await axios
                .put(this.endpoints.item, rest)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.item = {
                            ...response.data.item,
                            city_id: response.data.item.city.id
                        };
                        this.$notify.success({
                            title: 'Success',
                            message: 'Changes saved',
                        });

                        // location.href = '/admin/items';
                    } else {
                        this.$notify.error({
                            title: 'Error',
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },

        async saveDetails() {
            await axios
                .post(this.endpoints.details, this.detail)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.$notify.success({
                            title: 'Success',
                            message: 'Details saved',
                        });

                        this.$refs.loadingScreen.load(this.loadItem());
                        this.$refs.loadingScreen.load(this.loadCities());
                        this.$refs.loadingScreen.load(this.loadCategories());
                        this.$refs.loadingScreen.load(this.loadDetails());
                        this.dialogVisible = false;
                    } else {
                        this.$notify.error({
                            title: 'Error',
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async deleteDetail(item_detail_id) {
            if (confirm('Do you really want to delete this detail?')) {
                await axios
                    .delete('/ajax/item/details/', {
                        data: { item_detail_id: item_detail_id }
                    })
                    .then(result => {
                        this.loadDetails();
                    })
                    .catch(error => {
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong',
                            duration: 2000,
                        });
                    });
            }
        },
        checkfields(formName) {
            this.$refs[formName].validate(valid => {
                if (valid) {
                    switch (formName) {
                        case 'item':
                            this.$refs.loadingScreen.load(this.saveItem());
                            break;
                        case 'detail':
                            this.$refs.loadingScreen.load(this.saveDetails());
                            break;
                    }
                }
            });
        },
        sendingEvent(file, xhr, formData) {
            formData.append('item_id', this.item.item_id);
        },
        successUpload() {
            this.$refs.loadingScreen.load(this.loadItem());
            this.$refs.loadingScreen.load(this.loadCities());
            this.$refs.loadingScreen.load(this.loadCategories());
            this.$refs.loadingScreen.load(this.loadDetails());
            this.removePhotos();
        },
        removePhotos() {
            this.$refs.myVueDropzone.removeAllFiles();
        },
        handleClose(done) {
            this.$confirm('Are you sure to close this dialog?')
                .then(_ => {
                    done();
                })
                .catch(_ => {});
        },

        thumbnailSrc(item) {
            return (item.image) ? item.image.url : '/images/user-default.png'
        },

        getItemDetailType(itemDetail) {
            if (itemDetail.recommendation) {
                return `Recommendation by ${itemDetail.recommendation.user.full_name}`
            }

            if (itemDetail.isVerifiedBusinessDetail) {
                return `System - Verified Business Item`
            }

            return `System`
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2)
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        statusRow({row, rowIndex}) {
            if (! row.recommendation) return null
            if (row.status === 'rejected') return 'warning-row'
            if (row.status === 'approved') return 'success-row'

            return ''
        },

        async updateItemDetailStatus(itemDetail, status) {
            if (! this.item.image && status === 'approved') {
                this.$notify.error({
                    title: 'Error',
                    message: 'You must upload an image before you can approve this item detail.',
                    duration: 2000,
                })

                return
            }

            const itemDetailId = itemDetail.item_detail_id

            await this.$confirm(`This will set the status to ${status}. Continue?`, 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios
                    .put(`/ajax/details/${itemDetailId}`, {
                        status: status
                    })
                    .then(result => {
                        this.$notify.success({
                            title: 'Success',
                            message: `Item Detail is now ${status}`,
                            duration: 2000
                        })

                        this.$refs.loadingScreen.load(this.loadDetails());
                    })
                    .catch(error => {
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong',
                            duration: 2000,
                        })
                    })
            })
        },
    },
    watch: {
        filters: {
            handler() {
                this.loadDetails()
            },
            deep: true
        }
    },
    mounted() {
        $('.items').addClass('active');
        this.item.item_id = $('#item_id').val();
        this.detail.item_id = $('#item_id').val();
        this.$refs.loadingScreen.load(this.loadItem());
        this.$refs.loadingScreen.load(this.loadCities());
        this.$refs.loadingScreen.load(this.loadCategories());
        this.$refs.loadingScreen.load(this.loadDetails());
    },
};
</script>
<style>
.el-table .warning-row {
    background: oldlace;
}

.el-table .success-row {
    background: #f0f9eb;
}
</style>
