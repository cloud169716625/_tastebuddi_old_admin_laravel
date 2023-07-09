<template>
    <div id="item-list-wrapper">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    <a href="/admin/items/">
                        Items
                    </a>

                </li>

            </ol>

        </div>

        <div class="content p-20">

            <div class="d-flex justify-content-end">

                <button
                    class="btn btn-primary"
                    @click.stop.prevent="addItem()">
                    Add Item
                </button>

            </div>

            <loading-screen ref="loadingScreen">

                <table class="table table-borderless">

                    <thead>
                    <tr class="header">
                        <th></th>
                        <th>

                            <el-form>

                                <a href="javascript:;" @click="sort('item_name')">

                                    <h5 class="d-inline-block">Name</h5>

                                    <i
                                        class="fa"
                                        :class="{
                                        'fa-caret-up' : sortDirection,
                                         'fa-caret-down' : !sortDirection }"
                                        v-if="currentSort == 'item_name'">
                                    </i>

                                </a>

                                <el-form-item label="" prop="searchQueryName">

                                    <el-input
                                        v-model="searchQueryName"
                                        placeholder="Item Name"
                                        @keyup.native="search"
                                        clearable
                                        @clear="search">
                                    </el-input>

                                </el-form-item>

                            </el-form>

                        </th>
                        <th>

                            <el-form>

                                <h5 class="d-inline-block">City</h5>

                                <el-form-item label="" prop="searchQueryCity">

                                    <el-select
                                        v-model="searchQueryCity"
                                        placeholder="City"
                                        loading-text="loading cities..."
                                        clearable
                                        filterable
                                        @change="search"
                                        @clear="search">

                                        <el-option
                                            v-for="(value, key) in cities"
                                            :key="key"
                                            :label="value.city_name"
                                            :value="value.city_id">
                                        </el-option>

                                    </el-select>

                                </el-form-item>

                            </el-form>

                        </th>
                        <th>

                            <el-form>

                                <h5 class="d-inline-block">Category</h5>

                                <el-form-item label="" prop="searchQueryCategory">

                                    <el-select
                                        v-model="searchQueryCategory"
                                        placeholder="Category"
                                        loading-text="loading categories..."
                                        clearable
                                        filterable
                                        @change="search"
                                        @clear="search">

                                        <el-option
                                            v-for="(value, key) in categories"
                                            :key="key"
                                            :label="value.category_name"
                                            :value="value.category_id">
                                        </el-option>

                                    </el-select>

                                </el-form-item>

                            </el-form>

                        </th>

                        <th>

                            <el-form>

                                <h5 class="d-inline-block">Remarks</h5>

                                <el-form-item label="" prop="remarks">

                                    <el-select
                                        v-model="remarks"
                                        placeholder="Remarks"
                                        clearable
                                        @change="search"
                                        @clear="search">

                                        <el-option value="1" label="Needs Review"></el-option>
                                        <el-option value="0" label="Reviewed"></el-option>

                                    </el-select>

                                </el-form-item>

                            </el-form>

                        </th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr
                        v-for="( value , key ) in items.data"
                        :key="key"
                        class="pointer">

                        <td @click="goToItem( value.item_id )">

                            <img :src="thumbnailSrc(value)" :alt="value.item_name" style="max-width: 150px;"/>

                        </td>

                        <td @click="goToItem( value.item_id )" class="align-middle">

                            {{value.item_name}}

                        </td>

                        <td @click="goToItem( value.item_id )" class="align-middle">

                            {{ getCityName(value.city_id) }}

                        </td>

                        <td @click="goToItem( value.item_id )" class="align-middle">

                            {{ getCategoryName(value.category_id) }}

                        </td>

                        <td @click="goToItem( value.item_id )" class="align-middle">

                            {{ getRemarks(value.details_count) }}

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

                                        <a
                                            class="black"
                                            href="javascript:"
                                            @click="openItemDeletionModal( value.item_id )">
                                            <i class="icon-trash" ></i> Delete
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
                :data="items"
                @pagination-change-page="getResults"
                :limit="5">
            </pagination>
        </div>

        <el-dialog
            title="Add Item"
            :visible.sync="dialogVisible"
            width="60%"
            :before-close="handleClose">
            <el-form :model="item" ref="item" :rules="rules" label-width="120px" class="pr-5 pt-5">
                <el-form-item label="Name" prop="item_name" class>
                    <el-input v-model="item.item_name"></el-input>
                </el-form-item>

                <el-form-item label="Photos">
                    <vue-dropzone
                        ref="myVueDropzone"
                        id="dropzone"
                        :options="dropzoneOptions"
                        v-on:vdropzone-sending="sendingEvent"
                        v-on:vdropzone-queue-complete="afterProcess"
                        class="col"
                    ></vue-dropzone>
                </el-form-item>

                <el-form-item label="Category" prop="category_id" class>
                    <el-select
                        v-model="item.category_id"
                        placeholder="Select a Category"
                        loading-text="loading categories..."
                        clearable
                        filterable
                    >
                        <el-option
                            v-for="item in categories"
                            :key="item.category_id"
                            :label="item.category_name"
                            :value="item.category_id"
                        ></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="City" prop="city_id" class>
                    <el-select
                        v-model="item.city_id"
                        placeholder="Select a City"
                        loading-text="loading cities..."
                        clearable
                        filterable>
                        <el-option
                            v-for="item in cities"
                            :key="item.city_id"
                            :label="item.city_name"
                            :value="item.city_id"
                        ></el-option>
                    </el-select>
                </el-form-item>

            </el-form>

            <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">Cancel</el-button>
            <el-button type="primary" @click="checkfields('item')">Save</el-button>
        </span>
        </el-dialog>

        <div id="deleteItemModal" class="modal fade">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title"></h4>

                    </div>

                    <div class="modal-body text-center">

                        <i class="icon-warning icon-2x" style="color: orange;" ></i> Are you sure you want to delete item: <br><b>{{ item.item_name }}</b>?

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="triggerDeleteItem">
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
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import LoadingScreen from 'vue-loading-screen';
import Pagination from 'laravel-vue-pagination';

export default{
    name: 'item-list',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen,
        Pagination,
    },
    data() {
        return {
            items: {},
            item_id: null,
            item: {
                item_id: null,
                item_name: '',
                city_id: null,
                category_id: null,
                details_count: 0,
            },
            remarks: null,
            categories: [],
            cities: [],
            searchQueryName : '',
            searchQueryCity : null,
            searchQueryCategory : null,
            pageLimit: 10,
            dialogVisible: false,
            currentSort: 'item_name',
            sortDirection: true, //true = ASC, false = DESC
            endpoints:{
                item: '/ajax/item',
                items: '/ajax/items',
                getCities: '/ajax/item/cities',
                getCategories: '/ajax/item/categories',
            },
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
            rules: {
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
            }
        }
    },
    methods:{
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
            await axios
                .post(this.endpoints.item, this.item)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.item = response.data.item;
                        this.$refs.myVueDropzone.processQueue();
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
                    this.$refs.loadingScreen.load(this.saveItem());
                }
            });
        },
        sendingEvent(file, xhr, formData) {
            formData.append('item_id', this.item.item_id);
        },
        afterProcess() {
            this.$notify.success({
                title: 'Success',
                message: 'Changes saved',
            });
            this.$refs.loadingScreen.load(this.getResults());
        },
        async getResults(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }
            axios.get( `${this.endpoints.items}?page=${page}`, {
                params: {
                    searchQueryName: this.searchQueryName,
                    searchQueryCity: this.searchQueryCity,
                    searchQueryCategory: this.searchQueryCategory,
                    queryNeedsReview: this.remarks,
                    pageLimit: this.pageLimit,
                    currentSort : this.currentSort,
                    sortDirection : this.sortDirection
                }
            })
                .then(( result ) => {
                    let response = result.data;

                    if( response.success ){
                        this.items = response.data.items;

                    }else{
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
        openItemEditModal(item) {
            this.item = item;
            this.dialogVisible = true;
        },
        openItemDeletionModal( item_id ) {
            this.itemSelected( item_id );
            $('#deleteItemModal').modal();
        },
        triggerDeleteItem() {
            this.$refs.loadingScreen.load(this.deleteItem());
        },
        async deleteItem() {
            await axios.delete( this.endpoints.item, {
                params : {
                    item_id : this.item.item_id
                }
            })
                .then(( result ) => {
                    let response = result.data;
                    if( response.success ){
                        _.remove(this.items, {
                            item_id: this.item.item_id
                        });
                        $('#deleteItemModal').modal( 'toggle' );

                        this.$notify.error({
                            title: 'Deleted',
                            message: 'Item deleted!',
                        });
                        this.$refs.loadingScreen.load(this.getResults());
                    }else{
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
        goToItem( item_id ) {
            location.href =`/admin/items/${item_id}`;
        },
        addItem() {
            location.href = '/admin/items/create';
        },
        itemSelected( item_id ) {
            this.item = _.find(this.items.data, {item_id});
        },
        resetItem() {
            this.item = {
                item_id: null,
                item_name: '',
                city_id: null,
                category_id: null,
            };
        },
        search() {
            this.getResults();
        },
        sort(attribute) {
            this.currentSort = attribute;
            this.sortDirection = !this.sortDirection;
            this.$refs.loadingScreen.load(this.getResults());
        },
        handleOpenAddItem() {
            this.dialogVisible = true;
        },
        handleClose(done) {
            this.$confirm('Are you sure to close this dialog?')
                .then(_ => {
                    done();
                })
                .catch(_ => {});
        },
        thumbnailSrc(item) {
            return (item.image_url) ? item.image_url : '/images/user-default.png'
        },
        getCityName(cityId) {
            const cityIndex = this.cities.findIndex(city => city.city_id === cityId)

            return this.cities[cityIndex] && this.cities[cityIndex]['city_name']
        },
        getCategoryName(categoryId) {
            const categoryIndex = this.categories.findIndex(category => category.category_id === categoryId)

            return this.categories[categoryIndex] && this.categories[categoryIndex]['category_name']
        },
        getRemarks(detailsCount) {
            if (detailsCount > 0) return 'Needs Review'

            return 'Reviewed'
        }
    },
    mounted() {
        this.$refs.loadingScreen.load(this.getResults());
        this.$refs.loadingScreen.load(this.loadCountries());
        this.$refs.loadingScreen.load(this.loadCategories());
    }
}
</script>
