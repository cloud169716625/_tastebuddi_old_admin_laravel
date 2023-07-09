<template>
    <div id="country-create-content">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    <a href="/admin/countries/">
                        Countries
                    </a>
                </li>

                <li class="breadcrumb-item">
                    Add Country
                </li>

            </ol>

        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">

                    <hgroup>
                        <h2>
                            Add Country
                        </h2>
                    </hgroup>

                </div>

                <!-- card body -->
                <div class="text item">

                    <loading-screen ref="loadingScreen">

                        <el-form
                            :model="country"
                            :rules="rules"
                            ref="country"
                            label-width="160px"
                            class="pr-5 pt-5">

                            <el-form-item label="Country" prop="country_name" >

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

                            <el-form-item label="Alpha Code 2" prop="country_alpha_code_2">

                                <el-input
                                    v-model="country.country_alpha_code_2"
                                    placeholder="Alpha Code 2"
                                    clearable>
                                </el-input>

                            </el-form-item>

                            <el-form-item label="Alpha Code 3" prop="country_alpha_code_3">

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

                                <el-button
                                    type="primary"
                                    @click="checkfields('country')">
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
    name: 'country-create',
    components: {
        LoadingScreen
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
                        location.href = '/admin/countries';
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
        }
    },
    mounted() {
        $('.countries').addClass('active');
    }
}
</script>
