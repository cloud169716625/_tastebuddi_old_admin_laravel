<template>
    <div id="country-info-content">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item">

                    <a href="/admin/countries/">
                        Countries
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    {{ country.country_name }}

                </li>

            </ol>

            <div class="action-buttons"></div>

        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">

                    <hgroup>

                        <h2>{{country.country_name}}</h2>

                    </hgroup>

                </div>

                <div class="text item">

                    <loading-screen ref="loadingScreen">

                        <el-form
                            :model="country"
                            :rules="rules"
                            ref="country"
                            label-width="160px"
                            class="pr-5 pt-5">

                            <el-form-item label="Background Photo" prop="background_url">

                                <div v-if="!country.background_url || editableBackgroundPhoto" class="col-lg-4 mb-2">
                                    <div class="form-group mb-0">

                                        <label for="backgroundPhoto" class="mb-2">
                                            (<a class="text-danger"
                                                href
                                                @click.stop.prevent="editableBackgroundPhoto = false">
                                            <i class="text-danger icon-image4"></i> Cancel</a>)
                                        </label>

                                        <vue-dropzone
                                            ref="backgroundPhoto"
                                            id="backgroundPhoto"
                                            :options="backgroundPhotoDropzoneOptions"
                                            v-on:vdropzone-sending="sendingEvent"
                                            v-on:vdropzone-success="successUpload">
                                        </vue-dropzone>

                                    </div>

                                </div>

                                <div v-else class="col-lg-4 mb-2">

                                    <div class="form-group mb-0">

                                        <label class="mb-2">
                                            (
                                            <a class="text-primary" @click.stop.prevent="editableBackgroundPhoto = true">
                                                <i class="icon-image4 text-primary"></i> Change Photo
                                            </a>)
                                        </label>

                                        <div>

                                            <img
                                                :src="country.background_url"
                                                :alt="country.country_name"
                                                class="img-fluid img-thumbnail"
                                                height="150"/>

                                        </div>

                                    </div>

                                </div>

                            </el-form-item>

                            <el-form-item label="Flag Photo">

                                <div v-if="!country.flag_url || editableFlagPhoto" class="col-lg-4 mb-2">

                                    <div class="form-group mb-0">

                                        <label for="flagPhoto" class="mb-2">
                                            (
                                            <a class="text-danger" href @click.stop.prevent="editableFlagPhoto = false">
                                                <i class="text-danger icon-image4"></i> Cancel
                                            </a>)
                                        </label>

                                        <vue-dropzone
                                            ref="flagPhoto"
                                            id="flagPhoto"
                                            :options="flagPhotoDropzoneOptions"
                                            v-on:vdropzone-sending="sendingEvent"
                                            v-on:vdropzone-success="successUpload">
                                        </vue-dropzone>

                                    </div>

                                </div>

                                <div v-else class="col-lg-4 mb-2">

                                    <div class="form-group mb-0">

                                        <label class="mb-2">
                                            (
                                            <a class="text-primary" @click.stop.prevent="editableFlagPhoto = true">
                                                <i class="icon-image4 text-primary"></i> Change Photo
                                            </a>)
                                        </label>

                                        <div>

                                            <img
                                                :src="country.flag_url"
                                                :alt="country.country_name"
                                                class="img-fluid img-thumbnail"
                                                height="150"/>

                                        </div>

                                    </div>

                                </div>

                            </el-form-item>

                            <el-form-item label="Country" prop="country_name">

                                <el-input
                                    v-model="country.country_name"
                                    placeholder="Country Name"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Full Name" prop="full_name">

                                <el-input
                                    v-model="country.full_name"
                                    placeholder="Full Name"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Capital" prop="capital">

                                <el-input
                                    v-model="country.capital"
                                    placeholder="Capital"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Numeric Code" prop="country_numeric_code">

                                <el-input
                                    v-model="country.country_numeric_code"
                                    placeholder="Numeric Code"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item
                                label="Alpha Code 2"
                                prop="country_alpha_code_2">
                                <el-input
                                    v-model="country.country_alpha_code_2"
                                    placeholder="Alpha Code 2"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item
                                label="Alpha Code 3"
                                prop="country_alpha_code_3">

                                <el-input
                                    v-model="country.country_alpha_code_3"
                                    placeholder="Alpha Code 3"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Symbol Native" prop="symbol_native">

                                <el-input
                                    v-model="country.symbol_native"
                                    placeholder="Symbol Native"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Top Level Domain" prop="tl_domain">

                                <el-input
                                    v-model="country.tl_domain"
                                    placeholder="Top Level Domain"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Currency Code" prop="currency_code">

                                <el-input
                                    v-model="country.currency_code"
                                    placeholder="Currency Code"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Currency Name" prop="currency_name">

                                <el-input
                                    v-model="country.currency_name"
                                    placeholder="Currency Name"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item class="mt-2">
                                <el-button type="primary" @click="checkfields('country')">
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
import LoadingScreen from 'vue-loading-screen';
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';

export default {
    name: 'country-info',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen,
    },
    data() {
        return {
            country_id: null,
            country: {
                full_name: '',
                country_name: '',
                capital: '',
                country_numeric_code: '',
                country_alpha_code_2: '',
                country_alpha_code_3: '',
                tl_domain: '',
                symbol_native: '',
                currency_code: '',
                currency_name: '',
            },
            endpoints: {
                country: '/ajax/country',
                countries: '/ajax/countries',
            },
            backgroundPhotoDropzoneOptions: {
                url: '/ajax/country/background_photo/upload',
                thumbnailWidth: 150,
                maxFilesize: 1,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dictDefaultMessage:
                    '<i class="icon-cloud icon-2x"></i><br><small>Click to upload photo.</small>',
                maxFiles: 1,
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
            },
            flagPhotoDropzoneOptions: {
                url: '/ajax/country/flag_photo/upload',
                thumbnailWidth: 150,
                maxFilesize: 1,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dictDefaultMessage:
                    '<i class="icon-cloud icon-2x"></i><br><small>Click to upload photo.</small>',
                maxFiles: 1,
                acceptedFiles: 'image/png',
                addRemoveLinks: true,
            },
            editableBackgroundPhoto: false,
            editableFlagPhoto: false,
            rules: {
                country_name: [{
                    required: true,
                    message: 'Country Name is required',
                    trigger: 'blur',
                }, {
                    max: 45,
                    message: 'Length is up to 45 characters only',
                    trigger: 'blur',
                }],
                full_name: [{
                    max: 150,
                    message: 'Length is up to 150 characters only',
                    trigger: 'blur',
                }],
                capital: [{
                    required: true,
                    message: 'Capital is required',
                    trigger: 'blur',
                }, {
                    max: 45,
                    message: 'Length is up to 45 characters only',
                    trigger: 'blur',
                }],
                country_numeric_code: [{
                    max: 10,
                    message: 'Length is up to 10 characters only',
                    trigger: 'blur',
                }],
                country_alpha_code_2: [{
                    max: 2,
                    message: 'Length is up to 2 characters only',
                    trigger: 'blur',
                }],
                country_alpha_code_3: [{
                    max: 3,
                    message: 'Length is up to 3 characters only',
                    trigger: 'blur',
                }],
                tl_domain: [{
                    max: 3,
                    message: 'Length is up to 3 characters only',
                    trigger: 'blur',
                }],
                currency_name: [{
                    required: true,
                    message: 'Currency name is required',
                    trigger: 'blur',
                }, {
                    max: 45,
                    message: 'Length is up to 3 characters only',
                    trigger: 'blur',
                }],
                currency_code: [{
                    required: true,
                    message: 'Currency code is required',
                    trigger: 'blur',
                }, {
                    max: 3,
                    message: 'Length is up to 3 characters only',
                    trigger: 'blur',
                }]
            }
        }
    },
    methods: {
        async getCountry() {
            await axios
                .get(this.endpoints.country, { params: { cid: this.cid } })
                .then(result => {
                    let data = result.data.data;
                    this.country = data.country;
                    this.country_id = this.country.country_id;
                })
                .catch(error => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                });
        },
        async saveCountry() {
            await axios
                .post(this.endpoints.country, this.country)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.country = response.data.country;
                        this.$notify.success({
                            title: 'Success',
                            message: 'Changes saved',
                        });
                        this.$refs.loadingScreen.load(this.getCountry());
                        location.href = '/admin/countries/';
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
                if (valid) {
                    this.$refs.loadingScreen.load(this.saveCountry());
                }
            });
        },
        sendingEvent(file, xhr, formData) {
            formData.append('country_id', this.country_id);
        },
        successUpload() {
            this.editableBackgroundPhoto = false;
            this.editableFlagPhoto = false;
            this.$refs.loadingScreen.load(this.getCountry());
        }
    },
    mounted() {
        this.cid = $('#cid').val();
        $('.countries').addClass('active');
        this.$refs.loadingScreen.load(this.getCountry());
    }
}
</script>
