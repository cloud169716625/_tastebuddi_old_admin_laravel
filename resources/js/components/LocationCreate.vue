<template>
    <div id="location-create-content">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item">

                    <a href="/admin/locations/">
                        Locations
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Add Location
                </li>
            </ol>

        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">

                    <hgroup>
                        <h2>Add Location</h2>
                    </hgroup>

                </div>

                <!-- card body -->
                <div class="text item">

                    <loading-screen ref="loadingScreen">

                        <el-form
                            :model="location"
                            :rules="rules"
                            ref="location"
                            label-width="160px"
                            class="pr-5 pt-5">

                            <el-form-item label="Map">

                                <div>

                                    <h5>Search location</h5>

                                    <label class="search-location">
                                        <gmap-autocomplete
                                            @place_changed="setPlace">
                                        </gmap-autocomplete>
                                    </label>

                                    <br/>

                                </div>

                                <br>

                                <gmap-map
                                    :center="center"
                                    :zoom="12"
                                    style="width:100%;  height: 400px;">

                                    <gmap-marker
                                        :key="index"
                                        v-for="(m, index) in markers"
                                        :position="m.position"
                                        @click="center=m.position">
                                    </gmap-marker>

                                </gmap-map>

                            </el-form-item>

                            <el-form-item label="Location">

                                <el-input v-model="location.name" placeholder="Location" readonly></el-input>

                            </el-form-item>

                            <el-form-item label="City"  prop="city_id">

                                <el-select
                                    v-model="location.city_id"
                                    placeholder="Select a City"
                                    loading-text="loading cities..."
                                    clearable
                                    filterable>

                                    <el-option
                                        v-for="item in cities"
                                        :key="item.city_id"
                                        :label="item.city_name"
                                        :value="item.city_id">
                                    </el-option>

                                </el-select>

                            </el-form-item>

                            <el-form-item
                                label="Address"
                                prop="formatted_address"
                                v-if="location.formatted_address">

                                <el-input
                                    v-model="location.formatted_address"
                                    placeholder="Address"
                                    readonly>
                                </el-input>

                            </el-form-item>

                            <el-form-item
                                label="Place ID"
                                prop="place_id"
                                v-if="location.place_id">

                                <el-input
                                    v-model="location.place_id"
                                    placeholder="Place ID"
                                    readonly>
                                </el-input>

                            </el-form-item>

                            <el-form-item
                                label="Latitude Coordinate"
                                prop="geometry.location.lat"
                                v-if="location.geometry.location.lat">

                                <el-input
                                    v-model="location.geometry.location.lat()"
                                    placeholder="Latitude Coordinate"
                                    readonly>
                                </el-input>

                            </el-form-item>

                            <el-form-item
                                label="Longitude Coordinate"
                                prop="geometry.location.lng"
                                v-if="location.geometry.location.lng">

                                <el-input
                                    v-model="location.geometry.location.lng()"
                                    placeholder="Longitude Coordinate"
                                    readonly>
                                </el-input>

                            </el-form-item>

                            <el-form-item class="mt-2">

                                <el-button
                                    type="primary"
                                    @click="checkfields('location')">
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
        name: 'location-create',
        components: {
            LoadingScreen
        },
        data() {
            return {
                cities: [],
                center: {
                    lat: 13.748143,
                    lng: 100.501956,
                },
                markers: [],
                places: [],
                location: {
                    place_id: null,
                    formatted_address: null,
                    name: null,
                    geometry: {
                        location: {
                            lat: null,
                            lng: null,
                        }
                    },
                    city_id: null,
                },
                marker: null,
                endpoints: {
                    location: '/ajax/location',
                    locations: '/ajax/locations',
                    getCities: '/ajax/item/cities',
                },
                rules: {
                    name: [{
                        required: true,
                        message: 'Location is required',
                        trigger: 'blur',
                    }],

                    city_id: [{
                        required: true,
                        message: 'City is required',
                        trigger: 'change',
                    }],

                    place_id: [{
                        required: true,
                        message: 'Place ID is required',
                        trigger: 'blur',
                    }],

                    formatted_address: [{
                        required: true,
                        message: 'Address is required',
                        trigger: 'blur',
                    }],

                    'geometry.location.lat': [{
                        required: true,
                        message: 'Latitude is required',
                        trigger: 'blur',
                    }],

                    'geometry.location.lng': [{
                        required: true,
                        message: 'Longitude is required',
                        trigger: 'blur',
                    }],
                },
                searchCountry: null
            }
        },
        methods: {
            async loadCities() {
                await axios
                    .get(this.endpoints.getCities, {
                        params: {
                            'country_name': this.searchCountry
                        }
                    })
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
            async saveLocation() {
                let loc = {
                    'location': this.location.name,
                    'address': this.location.formatted_address,
                    'place_id': this.location.place_id,
                    'lat_coordinate': this.location.geometry.location.lat(),
                    'lng_coordinate': this.location.geometry.location.lng(),
                    'city_id': this.location.city_id,
                };

                await axios
                    .post(this.endpoints.location, loc)
                    .then(result => {
                        let response = result.data;
                        if (response.success) {
                            this.location = response.data.location;
                            this.$notify.success({
                                title: 'Success',
                                message: 'Changes saved',
                            });
                            location.href = '/admin/locations';
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
                        this.$refs.loadingScreen.load(this.saveLocation());
                    }
                });
            },
            setPlace(place) {
                const country = this.findCountryByGoogleMapLocation(place)
                this.location = place;

                if (country) {
                    this.searchCountry = country
                    this.loadCities()
                } else {
                    this.$notify.error({
                        title: 'Error',
                        message: 'City cannot be filtered, no results from API',
                    });
                }

                this.addMarker();
            },
            findCountryByGoogleMapLocation(place) {
                const types = place.address_components.map(place => place.types)
                const addressComponentIndex = types.findIndex(type => type.includes('country'))

                return place.address_components[addressComponentIndex]['long_name']
            },
            addMarker() {
                if (this.location) {
                    if(this.markers.length > 0 || this.places.length > 0){
                        this.markers = [];
                        this.places = [];
                    }
                    let marker = {
                        lat: this.location.geometry.location.lat(),
                        lng: this.location.geometry.location.lng(),
                    }
                    this.markers.push({ position: marker });
                    this.places.push(this.location);
                    this.center = marker;
                }
            },
            geolocate: function() {
                navigator.geolocation.getCurrentPosition(position => {
                    this.center = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                });
            }
        },
        mounted() {
            $('.locations').addClass('active');
            this.$refs.loadingScreen.load(this.loadCities());
        }
    };
</script>

<style lang="scss" scoped>
    #map {
        width: 100%;
        height: 500px;
    }

    .search-location{
        input{
            padding: 0 10px;
            border: 1px solid #ddd;
        }
    }
</style>
