<template>
<div id="country-list-wrapper">

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

        </ol>

        <div class="action-buttons">

            <div class="search">

                <div class="input-group mb-3">

                    <select id="searchFilter" v-model="searchFilter">

                        <option value="country_name">
                            Country Name
                        </option>

                        <option value="capital">
                            Capital
                        </option>

                    </select>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search"
                        v-model="searchQuery"
                        aria-describedby="basic-addon2"
                        @keyup="search"
                        @mouseleave="search"/>

                </div>

            </div>

        </div>

    </div>

    <div class="content p-20">

        <loading-screen ref="loadingScreen">

            <table class="table table-borderless">

                <thead>

                <tr class="header">

                    <th></th>
                    <th>
                        <a href="javascript:;" @click="sort('country_name')">
                            Country
                            <i class="fa"
                               :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                               v-if="currentSort == 'country_name'"></i>
                        </a>
                    </th>
                    <th></th>

                </tr>

                </thead>

                <tbody>
                <tr v-for="( value , key ) in countries.data" :key="key" class="pointer">
                    <td>
                        {{value.emoji}}
                    </td>
                    <td @click="goToCountry( value.country_id )">
                        {{value.country_name}}
                    </td>
                    <td>

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
                                    <a
                                        class="black"
                                        href="javascript:"
                                        @click="openCountryDeletionModal( value.country_id )">

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
            :data="countries"
            @pagination-change-page="getResults"
            :limit="5">
        </pagination>
    </div>

    <div id="deleteCountryModal" class="modal fade">

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
                    <i class="icon-warning icon-2x" style="color: orange;"></i> Are you sure you want to delete country {{ country.country_name }}?
                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="deleteCountry">
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
import LoadingScreen from "vue-loading-screen";
import Pagination from "laravel-vue-pagination";
export default{
    name: 'country-list',
    components: {
        LoadingScreen,
        Pagination
    },
    data(){
        return{
            countries: {},
            country: {
                full_name: '',
                country_name: '',
                capital: '',
            },
            /**** pagination data ***/
            searchQuery : '',
            searchFilter: 'country_name',
            pageLimit: 10,
            currentSort: 'country_name',
            sortDirection: true, //true = ASC, false = DESC
            endpoints:{
                "countries": '/ajax/countries',
                "country": '/ajax/country',
            }
        }
    },
    methods:{
        async getResults(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }
            await axios.get( `${this.endpoints.countries}?page=${page}`, {
                params : {
                    searchQuery : this.searchQuery,
                    searchFilter: this.searchFilter,
                    pageLimit : this.pageLimit,
                    currentSort : this.currentSort,
                    sortDirection : this.sortDirection
                }
            })
                .then(( result ) => {
                    let response = result.data;
                    if( response.success ){
                        this.countries = response.data.countries;
                    }else{
                        this.$notify.error({
                            title:'Error',
                            message:response.message,
                        })
                    }
                }).catch(error => {
                    this.$notify.error({
                        title:'Error',
                        message:'Something went wrong',
                    })
                });
        },
        countrySelected( country_id ) {
            this.country = _.find(this.countries.data, {country_id});
        },
        openCountryDeletionModal( country_id ) {
            this.countrySelected( country_id );
            $('#deleteCountryModal').modal();
        },
        async deleteCountry() {
            await axios.delete( this.endpoints.country,
                {
                    params : {
                        cid : this.country.country_id
                    }
                })
                .then(( result ) => {
                    let response = result.data;
                    if ( response.success ){
                        _.remove(this.countries, {
                            country_id: this.countries.country_id
                        });
                        $('#deleteCountryModal').modal( 'toggle' );
                        this.$notify.success({
                            title:'Deleted',
                            message:'Country deleted',
                        })
                        this.$refs.loadingScreen.load(this.getResults());

                    } else {
                        this.$notify.error({
                            title:'Error',
                            message:response.message,
                        })
                    }
                }).catch(error => {
                    this.$notify.error({
                        title:'Error',
                        message:'Something went wrong',
                    })
                });
        },
        goToCountry( country_id ) {
            location.href =`/admin/countries/${country_id}`;
        },
        resetCountry() {
            this.country = {
                full_name: '',
                country_name: '',
                capital: '',
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
