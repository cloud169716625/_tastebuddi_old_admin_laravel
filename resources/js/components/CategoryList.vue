<template>
    <div id="category-list-wrapper">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    <a href="/admin/categories">
                        Categories
                    </a>

                </li>

            </ol>

            <div class="action-buttons">

                <div class="search">

                    <div class="input-group mb-3">

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search"
                            aria-label=""
                            v-model="searchQuery"
                            aria-describedby="basic-addon2"
                            @keyup="search"
                            @mouseleave="search">

                    </div>

                </div>

            </div>
        </div>

        <hr>
        <div class="content p-20">
            <div class="d-flex justify-content-end">
                <button
                    class="btn btn-primary"
                    @click.stop.prevent="handleOpenAddCategory">
                    Add category
                </button>
            </div>
            <loading-screen ref="loadingScreen">

                <table class="table table-borderless">

                    <thead>
                    <tr class="header">
                        <th>
                            Category
                        </th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr
                        v-for="( value , key ) in categories.data"
                        :key="key"
                        class="pointer">
                        <td class="align-middle">
                            {{value.category_name}}
                        </td>
                        <td align="center" width="200px">
                            <el-button-group>
                                <el-button type="primary" size="small" @click="moveOrder(value.category_id, true)" icon="el-icon-caret-top"></el-button>
                                <el-button type="primary" size="small" @click="moveOrder(value.category_id, false)" icon="el-icon-caret-bottom"></el-button>
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <el-button type="primary" size="small" icon="el-icon-more"></el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item :command="{action: 'edit', value: value}"><i class="icon-pencil"></i> Edit</el-dropdown-item>
                                        <el-dropdown-item :command="{action: 'delete', value: value.category_id}"><i class="icon-trash"></i> Delete</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </el-button-group>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </loading-screen>

            <pagination
                :data="categories"
                @pagination-change-page="getResults"
                :limit="5">
            </pagination>

        </div>

        <el-dialog
            title="Add Category"
            :visible.sync="dialogVisible"
            width="40%"
            :before-close="handleClose">

            <el-form
                :model="category"
                ref="category"
                label-width="120px">

                <el-form-item label="Category Name">
                    <el-input v-model="category.category_name"></el-input>
                </el-form-item>

            </el-form>

            <span slot="footer" class="dialog-footer">

                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button
                    type="primary"
                    @click.stop.prevent="checkfields('category')">
                    Confirm
                </el-button>

            </span>

        </el-dialog>

        <div id="deleteCategoryModal" class="modal fade">
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
                        Are you sure you want to delete category {{ category.category_name }}?
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="triggerDeleteCategory">
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

export default {
    name: "category-list",
    components: {
        LoadingScreen,
        Pagination,
    },
    data() {
        return {
            categories: {},
            category: {
                category_name: "",
                category_id: null,
            },
            searchQuery : '',
            pageLimit: 10,
            currentSort: 'category_name',
            sortDirection: true, //true = ASC, false = DESC
            dialogVisible: false,
            endpoints: {
                categories: "/ajax/categories",
                category: "/ajax/category",
            },
        };
    },
    methods: {
        async getResults( page ) {
            if (typeof page === 'undefined') {
                page = 1;
            }
            await axios
                .get(`${this.endpoints.categories}?page=${page}`, {
                    params : {
                        searchQuery : this.searchQuery,
                        pageLimit : this.pageLimit,
                        currentSort : this.currentSort,
                        sortDirection : this.sortDirection
                    }
                })
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.categories = response.data.categories;
                    } else {
                        this.$notify.error({
                            title: "Error",
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: "Error",
                        message: "Something went wrong",
                    });
                });
        },
        categorySelected(category_id) {
            this.category = _.find(this.categories.data, {category_id});
        },
        openCategoryDeletionModal(category_id) {
            this.categorySelected(category_id);
            $("#deleteCategoryModal").modal();
        },
        openCategoryEditModal(category) {
            this.category = category;
            this.dialogVisible = true;
        },
        triggerDeleteCategory() {
            this.$refs.loadingScreen.load(this.deleteCategory());
        },
        async deleteCategory() {
            await axios
                .delete(this.endpoints.category, {
                    params: {
                        category_id: this.category.category_id
                    }
                })
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        _.remove(this.categories, {
                            category_id: this.category.category_id
                        });
                        $("#deleteCategoryModal").modal("toggle");
                        this.$refs.loadingScreen.load(this.getResults());
                        this.$notify.success({
                            title: "Deleted",
                            message: "Category deleted",
                        });
                    } else {
                        this.$notify.error({
                            title: "Error",
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: "Error",
                        message: "Something went wrong",
                    });
                });
        },
        openCategoryModal() {
            this.categorySelected(category_id);
            $("#editCategoryModal").modal();
        },

        goToCategory(category_id) {
            location.href = `/admin/categories/${category_id}`;
        },
        addCategory() {
            location.href = "/admin/categories/create";
        },
        resetCategory() {
            this.category = {};
        },
        search() {
            this.$refs.loadingScreen.load(this.getResults());
        },
        sort(attribute) {
            this.currentSort = attribute;
            this.sortDirection = !this.sortDirection;
            this.$refs.loadingScreen.load(this.getResults());
        },
        handleOpenAddCategory() {
            this.dialogVisible = true;
        },
        handleClose(done) {
            this.$confirm("Are you sure to close this dialog?")
                .then(_ => {
                    done();
                })
                .catch(_ => {});
        },
        async saveCategory() {
            await axios
                .post(this.endpoints.category, this.category)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.category = response.data.category;
                        this.$notify.success({
                            title: "Success",
                            message: "Changes saved",
                        });
                        this.category.category_name = "";
                        this.category.category_id = null;
                        this.dialogVisible = false;

                        this.$refs.loadingScreen.load(this.getResults());
                    } else {
                        this.$notify.error({
                            title: "Error",
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: "Error",
                        message: "Something went wrong",
                    });
                });
        },
        async updateCategory() {
            this.category.category_id = null;

            await axios
                .post(this.endpoints.category, this.category)
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.category = response.data.category;
                        this.$notify.success({
                            title: "Success",
                            message: "Changes saved",
                        });
                        this.category.category_name = "";
                        this.category.category_id = null;
                        this.dialogVisible = false;

                        this.$refs.loadingScreen.load(this.getResults());
                    } else {
                        this.$notify.error({
                            title: "Error",
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: "Error",
                        message: "Something went wrong",
                    });
                });
        },
        checkfields(formName) {
            this.$refs[formName].validate(valid => {
                if (valid) {
                    this.$refs.loadingScreen.load(this.saveCategory());
                }
            });
        },
        handleCommand(object) {
            const command = object.action
            const value = object.value

            if (command === 'edit') this.openCategoryEditModal(value)
            if (command === 'delete') this.openCategoryDeletionModal(value)
        },
        async moveOrder(categoryId, direction) {
            await axios
                .post(`/ajax/category/${categoryId}/order`, {
                    order: direction
                })
                .then(result => {
                    let response = result.data;
                    if (response.success) {
                        this.$notify.success({
                            title: "Success",
                            message: "Category Order Updated",
                        });

                        this.$refs.loadingScreen.load(this.getResults());
                    } else {
                        this.$notify.error({
                            title: "Error",
                            message: response.message,
                        });
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title: "Error",
                        message: "Something went wrong",
                    });
                });
        }
    },
    mounted: function() {
        this.$refs.loadingScreen.load(this.getResults());
    }
}
</script>
