<template>
    <div id="city-list-wrapper">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">
                    <a href="#">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    <a href="/admin/cities/">
                        Cities
                    </a>
                </li>

            </ol>

            <div class="action-buttons">

                <div class="search">

                    <div class="input-group mb-3">

                        <select id="searchFilter" v-model="searchFilter">

                            <option value="city_name">
                                City Name
                            </option>
                            <option value="city_code">
                                City Code
                            </option>

                        </select>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search"
                            aria-label=""
                            v-model="searchQuery"
                            aria-describedby="basic-addon2"
                            @keyup="search"
                            @mouseleave="search"/>

                    </div>

                </div>

            </div>

        </div>

        <hr>

        <div class="content p-20">

            <div class="d-flex justify-content-end">

                <button
                    class="btn btn-primary"
                    @click.stop.prevent="addCity()">
                    Add City
                </button>

            </div>

            <loading-screen ref="loadingScreen">

                <table class="table table-borderless">

                    <thead>

                    <tr class="header">

                        <th>
                            <a href="javascript:;" @click="sort('city_name')">
                                City
                                <i class="fa"
                                   :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                                   v-if="currentSort == 'city_name'"
                                ></i>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:;" @click="sort('city_code')">
                                City Code
                                <i class="fa"
                                   :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                                   v-if="currentSort == 'city_code'"
                                ></i>
                            </a>
                        </th>

                        <th></th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr
                        v-for="( value , key ) in cities.data"
                        :key="key"
                        class="pointer">

                        <td @click="goToCity( value.city_id )" class="align-middle">
                            {{value.city_name}}
                        </td>

                        <td @click="goToCity( value.city_id )" class="align-middle">
                            {{value.city_code}}
                        </td>

                        <td class="light-dark align-middle">
                            <div class="dropdown">
                                <button
                                    class="btn btn-default"
                                    type="button"
                                    id="dropdownMenu1"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="true">

                                    <i class="icon-menu"></i>

                                </button>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                                    <li class="dropdownList">

                                        <a class="black" href="javascript:" @click="openCityDeletionModal( value.city_id )">

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
                :data="cities"
                @pagination-change-page="getResults"
                :limit="5">
            </pagination>

        </div>

        <div id="deleteCityModal" class="modal fade">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true">
                            &times;
                        </button>

                        <h4 class="modal-title"></h4>
                    </div>

                    <div class="modal-body">

                        <i class="icon-warning icon-2x" style="color: orange;"></i>
                        Are you sure you want to delete city {{ city.city_name }}?

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="triggerDeleteCity">
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
    import LoadingScreen from 'vue-loading-screen';
    import Pagination from 'laravel-vue-pagination';

    export default {
        name: 'city-list',
        components: {
            LoadingScreen,
            Pagination,
        },
        data() {
            return {
                cities: {},
                city: {
                    city_name: '',
                    city_code: '',
                },
                searchQuery : '',
                searchFilter: 'city_name',
                pageLimit: 10,
                currentSort: 'city_name',
                sortDirection: true, //true = ASC, false = DESC
                /**** pagination data ***/
                endpoints: {
                    cities: '/ajax/cities',
                    city: '/ajax/city',
                }
            }
        },
        methods: {
            async getResults(page) {
                if (typeof page === 'undefined') {
                    page = 1;
                }
                await axios
                    .get(`${this.endpoints.cities}?page=${page}`, {
                        params : {
                            searchQuery : this.searchQuery,
                            searchFilter: this.searchFilter,
                            pageLimit : this.pageLimit,
                            currentSort : this.currentSort,
                            sortDirection : this.sortDirection
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
            citySelected(city_id) {
                this.city = _.find(this.cities.data, {city_id});
            },
            openCityDeletionModal(city_id) {
                this.citySelected(city_id);
                $('#deleteCityModal').modal();
            },
            triggerDeleteCity() {
                this.$refs.loadingScreen.load(this.deleteCity());
            },
            async deleteCity() {
                await axios
                    .delete(this.endpoints.city, {
                        params: {
                            cid: this.city.city_id
                        }
                    })
                    .then(result => {
                        let response = result.data;
                        if (response.success) {
                            _.remove(this.cities, {
                                city_id: this.cities.city_id
                            });
                            $('#deleteCityModal').modal('toggle');
                            this.$notify.success({
                                title: 'Deleted',
                                message: 'City deleted',
                            });
                            this.$refs.loadingScreen.load(this.getResults());

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

            goToCity(city_id) {
                location.href = `/admin/cities/${city_id}`;
            },
            addCity() {
                location.href = '/admin/cities/create';
            },
            resetCity() {
                this.city = {
                    city_name: '',
                    city_code: '',
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
        },
        mounted() {
            this.$refs.loadingScreen.load(this.getResults());
        }
    }
</script>
