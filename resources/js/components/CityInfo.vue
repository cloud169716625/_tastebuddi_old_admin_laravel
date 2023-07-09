<template>
    <div id="city-create-content">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">
                    <a href="#">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <a href="/admin/cities/">
                        Cities
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    {{city.city_name}}
                </li>

            </ol>

        </div>

        <hr />

        <div class="content p-20">
            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">
                    <hgroup>
                        <h2>{{ city.city_name }}</h2>
                    </hgroup>
                </div>

                <!-- card body -->
                <div class="text item ">

                    <loading-screen ref="loadingScreen">

                        <el-form
                            :model="city"
                            ref="city"
                            :rules="rules"
                            label-width="120px"
                            class="pr-5 pt-5">

                            <el-form-item label="Name" prop="city_name">

                                <el-input v-model="city.city_name"></el-input>

                            </el-form-item>

                            <el-form-item label="City Code" prop="city_code">

                                <el-input v-model="city.city_code"></el-input>

                            </el-form-item>

                            <el-form-item label="Country" prop="country_id">

                                <el-select
                                    v-model="city.country_id"
                                    placeholder="Select a Country"
                                    loading-text="loading countries..."
                                    clearable
                                    filterable>

                                    <el-option
                                        v-for="item in countries"
                                        :key="item.country_id"
                                        :label="item.country_name"
                                        :value="item.country_id">
                                    </el-option>

                                </el-select>

                            </el-form-item>

                            <el-form-item class="mt-2">

                                <el-button type="primary" @click="checkfields('city')">
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

    export default {
        name: 'city-info',
        components: {
            LoadingScreen
        },
        data() {
            return {
                city_id: null,
                city: {
                    country_id: '',
                    city_name: '',
                    city_code: '',
                },
                countries: [],
                endpoints: {
                    city: '/ajax/city',
                    cities: '/ajax/cities',
                    getCountries: '/ajax/city/countries',
                },
                selectedCountry: '',
                rules: {
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
                    country_id: [{
                        required: true,
                        message: 'Country is required',
                        trigger: 'change',
                    }]
                }
            }
        },
        methods: {
            async getCity() {
                await axios
                    .get(this.endpoints.city, { params: { cid: this.cid } })
                    .then(result => {
                        let data = result.data.data;
                        this.city = data.city;
                        this.cid = this.city.city_id;
                    })
                    .catch(error => {
                        this.$notify.error({
                            title: 'Error',
                            message: 'Something went wrong',
                        });
                    });
            },
            async loadCountries() {
                await axios
                    .get(this.endpoints.getCountries)
                    .then(result => {
                        let response = result.data;

                        if (response.success) {
                            this.countries = response.data.countries;
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
            async saveCity() {
                await axios
                    .post(this.endpoints.city, this.city)
                    .then(result => {
                        let response = result.data;
                        if (response.success) {
                            this.city = response.data.city;
                            this.$notify.success({
                                title: 'Success',
                                message: 'Changes saved',
                            });
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
                        this.$refs.loadingScreen.load(this.saveCity());
                    }
                });
            }
        },
        mounted() {
            this.cid = $('#cid').val();
            this.$refs.loadingScreen.load(this.loadCountries());
            this.$refs.loadingScreen.load(this.getCity());
        }
    }
</script>
