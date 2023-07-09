<template>
  <div id="location-create-content">
    <div class="page-header">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#"> Home </a>
        </li>

        <li class="breadcrumb-item">
          <a href="/admin/locations/"> Locations </a>
        </li>

        <li class="breadcrumb-item active">
          {{ location.name }}
        </li>
      </ol>
    </div>

    <hr />

    <div class="content p-20">
      <el-card :body-style="{ padding: '0px' }" shadow="none">
        <div slot="header">
            <el-row>
                <el-col :span="12">
                    <hgroup>
                        <h2>{{ location.name }}</h2>
                    </hgroup>
                </el-col>
                <el-col :span="12">
                    <div class="verified-business">
                        <el-checkbox v-model="is_business_verified" @change="verifyUnverify">Verified Business</el-checkbox><br/>
                        <span
                            class="vb-badge"
                            :class="{
                                'is-active': is_business_verified,
                                'is-inactive': !is_business_verified
                            }">
                            {{ is_business_verified ? 'active' : 'inactive' }}
                        </span>
                    </div>
                </el-col>
            </el-row>
        </div>

        <!-- card body -->
        <div class="text item">
          <loading-screen ref="loadingScreen">
            <el-form
              :model="location"
              :rules="rules"
              ref="location"
              label-width="160px"
              class="pr-5 pt-5"
            >
              <el-form-item label="Map">
                <div>
                  <h5>Search location</h5>
                  <label class="search-location">
                    <gmap-autocomplete @place_changed="setPlace">
                    </gmap-autocomplete>
                  </label>
                  <br />
                </div>

                <br />

                <gmap-map
                  :center="center"
                  :zoom="12"
                  style="width: 100%; height: 400px"
                  ref="mapRef"
                >
                  <gmap-marker
                    :key="index"
                    v-for="(m, index) in markers"
                    :position="m.position"
                    @click="center = m.position"
                  >
                  </gmap-marker>
                </gmap-map>
              </el-form-item>

              <el-form-item label="Location">
                <el-input
                  v-model="location.name"
                  placeholder="Location"
                  readonly
                >
                </el-input>
              </el-form-item>

              <el-form-item label="City" prop="city_id">
                <el-select
                  v-model="location.city_id"
                  placeholder="Select a City"
                  loading-text="loading cities..."
                  clearable
                  filterable
                >
                  <el-option
                    v-for="item in cities"
                    :key="item.city_id"
                    :label="item.city_name"
                    :value="item.city_id"
                  >
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item
                label="Address"
                prop="formatted_address"
                v-if="location.formatted_address"
              >
                <el-input
                  v-model="location.formatted_address"
                  placeholder="Address"
                  readonly
                >
                </el-input>
              </el-form-item>

              <el-form-item
                label="Place ID"
                prop="place_id"
                v-if="location.place_id"
              >
                <el-input
                  v-model="location.place_id"
                  placeholder="Place ID"
                  readonly
                >
                </el-input>
              </el-form-item>

              <el-form-item
                label="Latitude Coordinate"
                prop="geometry.location.lat"
                v-if="location.lat"
              >
                <el-input
                  v-model="location.lat"
                  placeholder="Latitude Coordinate"
                  readonly
                >
                </el-input>
              </el-form-item>

              <el-form-item
                label="Longitude Coordinate"
                prop="geometry.location.lng"
                v-if="location.lng"
              >
                <el-input
                  v-model="location.lng"
                  placeholder="Longitude Coordinate"
                  readonly
                >
                </el-input>
              </el-form-item>

              <el-form-item class="mt-2">
                <el-button type="primary" @click="checkfields('location')">
                  Save
                </el-button>
              </el-form-item>
            </el-form>
          </loading-screen>
        </div>
      </el-card>
    </div>
    <VerifiedBusinessItems :locationId="location_id" :cityId="location.city_id"/>
    <LocationItemDetails class="mt-4" :locationId="location_id" :cityId="location.city_id"/>
  </div>
</template>


<style lang="scss">
.el-checkbox__label{
    color: #8C919B !important;
}
.verified-business {
    position: relative;
    float: right;
    .vb-badge{
        position: relative;
        border-radius: 2px;
        height: 20px;
        float: right;
        color: #fff;
        border-radius: 2px;
        text-transform: uppercase;
        font-style: normal;
        font-weight: 700;
        font-size: 12px;
        padding: 2px 5px 2px 5px;
    }
    .is-active{
        background: rgba(64, 158, 255, 0.5);
    }
    .is-inactive{
        background: rgba(96, 90, 90, 0.5);;
    }
}
</style>

<script>
import { gmapApi } from "vue2-google-maps";
import LoadingScreen from "vue-loading-screen";
import VerifiedBusinessItems from './childComponents/VerifiedBusiness/ItemList.vue'
import LocationItemDetails from './childComponents/Location/ItemList.vue'

export default {
  name: "location-info",
  components: {
    LoadingScreen,
    VerifiedBusinessItems,
    LocationItemDetails
  },
  data() {
    return {
      location_id: null,
      is_business_verified: false,
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
          },
        },
        lat: null,
        lng: null,
        city_id: null,
      },
      marker: null,
      locations: [],
      points: [],
      cities: [],
      endpoints: {
        location: "/ajax/location",
        locations: "/ajax/locations",
        getAllLocation: "/ajax/locations/all",
        getCities: "/ajax/item/cities",
      },
      rules: {
        name: [
          {
            required: true,
            message: "Location is required",
            trigger: "blur",
          },
        ],

        city_id: [
          {
            required: true,
            message: "City is required",
            trigger: "change",
          },
        ],

        place_id: [
          { required: true, message: "Place ID is required", trigger: "blur" },
        ],

        formatted_address: [
          {
            required: true,
            message: "Address is required",
            trigger: "blur",
          },
        ],

        lat: [
          {
            required: true,
            message: "Latitude is required",
            trigger: "blur",
          },
        ],

        lng: [
          {
            required: true,
            message: "Longitude is required",
            trigger: "blur",
          },
        ],
      },
    };
  },
  methods: {
    async loadCities() {
      await axios
        .get(this.endpoints.getCities)
        .then((result) => {
          let response = result.data;
          if (response.success) {
            this.cities = response.data.cities;
          } else {
            this.$notify.error({
              title: "Error",
              message: response.message,
            });
          }
        })
        .catch((error) => {
          this.$notify.error({
            title: "Error",
            message: "Something went wrong",
          });
        });
    },

    async getLocation() {
      await axios
        .get(this.endpoints.location, {
          params: { location_id: this.location_id },
        })
        .then((result) => {
          let data = result.data.data;
          let tempMarker = null;

          this.location_id = data.location.location_id;
          this.location.name = data.location.location;
          this.location.formatted_address = data.location.address;
          this.location.place_id = data.location.place_id;
          this.location.lat = data.location.lat_coordinate;
          this.location.lng = data.location.lng_coordinate;
          this.location.city_id = data.location.city_id;
          this.is_business_verified = data.location.is_verified;
          this.searching = false;
          this.center.lat = parseFloat(this.location.lat);
          this.center.lng = parseFloat(this.location.lng);
          tempMarker = new google.maps.LatLng(this.center.lat, this.center.lng);
          this.markers.push({ position: tempMarker });
        })
        .catch((error) => {
          this.$notify.error({
            title: "Error",
            message: "Something went wrong",
          });
        });
    },
    async saveLocation() {
      let loc = {
        location_id: this.location_id,
        location: this.location.name,
        address: this.location.formatted_address,
        place_id: this.location.place_id,
        lat_coordinate: this.location.lat,
        lng_coordinate: this.location.lng,
        city_id: this.location.city_id,
      };

      await axios
        .post(this.endpoints.location, loc)
        .then((result) => {
          let response = result.data;
          if (response.success) {
            this.location = response.data.location;
            this.$notify.success({
              title: "Success",
              message: "Changes saved",
            });
            location.href = "/admin/locations";
          } else {
            this.$notify.error({
              title: "Error",
              message: response.message,
            });
          }
        })
        .catch((error) => {
          this.$notify.error({
            title: "Error",
            message: "Something went wrong",
          });
        });
    },

    checkfields(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$refs.loadingScreen.load(this.saveLocation());
        }
      });
    },
    setPlace(place) {
      this.location = place;
      this.addMarker();
    },

    addMarker() {
      if (this.location) {
        if (this.markers.length > 0 || this.places.length > 0) {
          this.markers = [];
          this.places = [];
        }
        let marker = {
          lat: this.location.geometry.location.lat(),
          lng: this.location.geometry.location.lng(),
        };
        this.location.lat = this.location.geometry.location.lat();
        this.location.lng = this.location.geometry.location.lng();
        this.markers.push({ position: marker });
        this.places.push(this.location);
        this.center = marker;
      }
    },
    geolocate: function () {
      navigator.geolocation.getCurrentPosition((position) => {
        this.center = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };
      });
    },
    async verifyUnverify() {
      await axios.post(`/ajax/locations/${this.location_id}/unverify-verify`);
    },
  },
  mounted() {
    $(".locations").addClass("active");
    this.location_id = $("#location_id").val();
    this.$refs.loadingScreen.load(this.getLocation());
    this.$refs.loadingScreen.load(this.loadCities());

    this.$refs.mapRef.$mapPromise.then((map) => {
      map.panTo({
        lat: this.center.lat,
        lng: this.center.lng,
      });
    });
  },
  computed: {
    google: gmapApi,
  },
};
</script>

<style lang="scss" scoped>
.search-location {
  input {
    padding: 0 10px;
    border: 1px solid #ddd;
  }
}
</style>
