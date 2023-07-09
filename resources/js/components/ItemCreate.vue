<template>
    <div id="item-create-content">

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

                <li class="breadcrumb-item active">
                    Add Item
                </li>

            </ol>

            <div class="action-buttons"></div>
        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">

                    <hgroup>
                        <h2>Add Item</h2>
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

                            <el-form-item label="Name" prop="item_name">

                                <el-input v-model="item.item_name"></el-input>

                            </el-form-item>

                            <el-form-item label="Photos">

                                <vue-dropzone
                                    ref="myVueDropzone"
                                    id="dropzone"
                                    :options="dropzoneOptions"
                                    v-on:vdropzone-sending="sendingEvent"
                                    v-on:vdropzone-queue-complete="afterProcess"
                                    class="col">
                                </vue-dropzone>

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

                            <el-form-item label="City" prop="city_id">

                                <el-select
                                    v-model="item.city_id"
                                    placeholder="Select a City"
                                    loading-text="loading cities..."
                                    clearable
                                    filterable
                                    @change="loadLocations()">

                                    <el-option
                                        v-for="item in cities"
                                        :key="item.city_id"
                                        :label="item.city_name"
                                        :value="item.city_id">
                                    </el-option>

                                </el-select>

                            </el-form-item>

                            <el-form-item label="Price" prop="price">

                                <el-input-number
                                    v-model="item.price"
                                    clearable
                                    :controls=false
                                    :precision="2">
                                </el-input-number>

                            </el-form-item>

                            <el-form-item label="Location" prop="location_id">

                                <el-select
                                    v-model="item.location_id"
                                    placeholder="Select a Location"
                                    loading-text="loading locations..."
                                    clearable
                                    filterable
                                    required>

                                    <el-option
                                        v-for="item in locations"
                                        :key="item.location_id"
                                        :label="item.location"
                                        :value="item.location_id">
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

                            <el-form-item class="mt-2">

                                <el-button
                                    type="primary"
                                    @click="checkfields('item')">
                                    Save
                                </el-button>

                            </el-form-item>
                        </el-form>
                    </loading-screen>
                </div>

            </el-card>

        </div>
    </div>
</template>
<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import LoadingScreen from 'vue-loading-screen';

export default {
    name: 'item-create',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen
    },
    data() {
        return {
            item_id: null,
            item: {
                item_id: null,
                item_name: '',
                city_id: null,
                category_id: null,
                price: 0,
                location_id: null,
                custom_price_high: null,
                custom_price_low: null
            },
            categories: [],
            cities: [],
            locations: [],
            endpoints: {
                item: '/ajax/item',
                items: '/ajax/items/',
                getCities: '/ajax/item/cities',
                getCategories: '/ajax/item/categories',
                locations: '/ajax/item/locations',
            },
            selectedCountry: null,
            selectedCategory: null,
            dropzoneOptions: {
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
                autoProcessQueue: false,
            },
        }
    },
    computed: {
        rules() {
            return {
                item_name: [{
                    required: true,
                    message: 'Item Name is required',
                    trigger: 'blur',
                }, {
                    max: 150,
                    message: 'Length is up to 150 characters only',
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
                price: [{
                    required: true,
                    message: 'Price is required',
                    trigger: 'blur',
                }],
                location_id: [{
                    required: true,
                    message: 'Location is required',
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
        async loadCountries() {
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
            await axios.post(this.endpoints.item, this.item)
                .then(result => {
                    let response = result.data;
                    if (response.data) {
                        this.item.item_id = response.data.item.item_id;
                        this.$refs.myVueDropzone.processQueue();

                        this.$notify.success({
                            title: 'Success',
                            message: 'Item successfully been created'
                        })

                        location.href = '/admin/items';

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
        checkfields(formName) {
            this.$refs[formName].validate(valid => {
                const vueDropzoneValid = this.$refs.myVueDropzone.getRejectedFiles()

                if (vueDropzoneValid.length) {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Please check your attachment.',
                    });

                    return
                }

                if (valid) {
                    this.$refs.loadingScreen.load(this.saveItem());
                }
            });
        },
        sendingEvent(file, xhr, formData) {
            formData.append('item_id', this.item.item_id);
        },
        afterProcess() {
            const vueDropzoneValid = this.$refs.myVueDropzone.getRejectedFiles()

            if (vueDropzoneValid.length > 0) {
                this.$notify.error({
                    title: 'Error',
                    message: 'Please check your attachment.',
                });
            }
            // location.href = '/admin/items';
        },
        async loadLocations() {
            await axios
                .get(this.endpoints.locations, {
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
    },
    mounted() {
        this.$refs.loadingScreen.load(this.loadCountries());
        this.$refs.loadingScreen.load(this.loadCategories());
    }
}
</script>
