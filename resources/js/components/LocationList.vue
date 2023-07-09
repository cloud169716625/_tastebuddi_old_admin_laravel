<template>
  <div id="location-list-wrapper">
    <div class="page-header">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#"> Home </a>
        </li>

        <li class="breadcrumb-item active">
          <a href="/admin/locations/"> Locations </a>
        </li>
      </ol>

      <div class="action-buttons">
        <div class="search">
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              placeholder="Search"
              v-model="searchQuery"
              aria-describedby="basic-addon2"
              @keyup="search"
              @mouseleave="search"
            />
          </div>
        </div>
      </div>
    </div>

    <hr />

    <div class="content p-20">
      <div class="d-flex justify-content-end">
        <button class="btn btn-primary" @click.stop.prevent="addLocation()">
          Add Location
        </button>
      </div>
      <br />
      <LocationListFilters @getFilters="setFilters" />
      <br />
      <loading-screen ref="loadingScreen">
        <table class="table table-borderless">
          <thead>
            <tr class="header">
              <th>
                <a href="javascript:;" @click="sort('location')">
                  Location
                  <i
                    class="fa"
                    :class="{
                      'fa-caret-up': sortDirection,
                      'fa-caret-down': !sortDirection,
                    }"
                    v-if="currentSort == 'location'"
                  ></i>
                </a>
              </th>
              <th>
                <a href="javascript:;" @click="sort('address')">
                  Address
                  <i
                    class="fa"
                    :class="{
                      'fa-caret-up': sortDirection,
                      'fa-caret-down': !sortDirection,
                    }"
                    v-if="currentSort == 'address'"
                  >
                  </i>
                </a>
              </th>
              <th></th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(value, key) in locations.data"
              :key="key"
              class="pointer"
            >
              <td @click="goToLocation(value.location_id)" class="align-middle">
                {{ value.location }}
              </td>
              <td @click="goToLocation(value.location_id)" class="align-middle">
                {{ value.address }}
              </td>
              <td>
                <div class="verified-business">
                    <span
                        class="vb-badge"
                        :class="{
                            'is-active': value.is_verified,
                            'is-inactive': !value.is_verified
                        }">
                        {{ value.is_verified ? 'verified' : 'unverified' }}
                    </span>
                </div>
              </td>
              <td class="light-dark align-middle">
                <div class="dropdown">
                  <button
                    class="btn btn-default"
                    type="button"
                    id="dropdownMenu1"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="true"
                  >
                    <i class="icon-menu"></i>
                  </button>

                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li class="dropdownList">
                      <a
                        class="black"
                        href="javascript:"
                        @click="openLocationDeletionModal(value.location_id)"
                      >
                        <i class="icon-trash"></i> Delete
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </loading-screen>

      <pagination
        :data="locations"
        @pagination-change-page="getResults"
        :limit="5"
      >
      </pagination>
    </div>

    <div id="deleteLocationModal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-hidden="true"
            >
              &times;
            </button>
            <h4 class="modal-title"></h4>
          </div>

          <div class="modal-body">
            <i class="icon-warning icon-2x" style="color: orange"></i>
            Are you sure you want to delete this location?<br />
            <b>Location: </b>{{ location.location }}
            <br />
            <b>Address: </b>{{ location.address }}
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              @click="triggerDeleteLocation"
            >
              Delete
            </button>

            <button type="button" class="btn btn-default" data-dismiss="modal">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
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
import LoadingScreen from "vue-loading-screen";
import Pagination from "laravel-vue-pagination";
import LocationListFilters from "./childComponents/LocationListFilters.vue";

export default {
  name: "location-list",
  components: {
    LoadingScreen,
    Pagination,
    LocationListFilters,
  },
  data() {
    return {
      locations: {},
      location: {
        location: "",
        address: "",
        lat_coordinate: null,
        lng_coordinate: null,
      },
      searchQuery: "",
      searchFilter: "location",
      pageLimit: 10,
      currentSort: "location",
      sortDirection: true, //true = ASC, false = DESC
      endpoints: {
        locations: "/ajax/locations",
        location: "/ajax/location",
      },
      filters: {},
    };
  },
  methods: {
    async getResults(page) {
      if (typeof page === "undefined") {
        page = 1;
      }
      await axios
        .get(`${this.endpoints.locations}?page=${page}`, {
          params: {
            searchQuery: this.searchQuery,
            searchFilter: this.searchFilter,
            pageLimit: this.pageLimit,
            currentSort: this.currentSort,
            sortDirection: this.sortDirection,
            filters: this.filters,
          },
        })
        .then((result) => {
          let response = result.data;
          if (response.success) {
            this.locations = response.data.locations;
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
    openLocationDeletionModal(location_id) {
      this.locationSelected(location_id);
      $("#deleteLocationModal").modal();
    },
    async triggerDeleteLocation() {
      await this.$refs.loadingScreen.load(this.deleteLocation());
    },
    async deleteLocation() {
      await axios
        .delete(this.endpoints.location, {
          params: { location_id: this.location.location_id },
        })
        .then((result) => {
          let response = result.data;
          if (response.success) {
            _.remove(this.locations, {
              location_id: this.locations.location_id,
            });
            $("#deleteLocationModal").modal("toggle");
            this.$notify.success({
              title: "Deleted",
              message: "Location deleted",
            });
            this.$refs.loadingScreen.load(this.getResults());
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
    openLocationModal() {
      this.locationSelected(cid);
      $("#editLocationModal").modal();
    },
    goToLocation(location_id) {
      location.href = `/admin/locations/${location_id}`;
    },
    addLocation() {
      location.href = "/admin/locations/create";
    },
    locationSelected(location_id) {
      this.location = _.find(this.locations.data, { location_id });
    },
    resetLocation() {
      this.location = {
        location: "",
        address: "",
        lat_coordinate: null,
        lng_coordinate: null,
      };
    },
    search() {
      this.$refs.loadingScreen.load(this.getResults());
    },
    sort(attribute) {
      this.currentSort = attribute;
      this.sortDirection = !this.sortDirection;
      this.$refs.loadingScreen.load(this.getResults());
    },
    setFilters(filters) {
      this.filters = filters;
      this.getResults();
    },
  },
  mounted() {
    this.$refs.loadingScreen.load(this.getResults());
  },
};
</script>

