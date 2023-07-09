<template>
<div id="user-list-wrapper">

    <div class="page-header">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="#">
                    Home
                </a>
            </li>

            <li class="breadcrumb-item active">
                <a href="/admin/users">
                    Users
                </a>
            </li>

        </ol>

        <div class="action-buttons">

            <div class="search">

                <div class="input-group mb-3">

                    <select id="searchFilter" v-model="searchFilter">

                        <option value="first_name">
                            First Name
                        </option>
                        <option value="last_name">
                            Last Name
                        </option>
                        <option value="email">
                            Email
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
                        @mouseleave="search">
                </div>

            </div>

        </div>

    </div>

    <div class="content p-20">

        <loading-screen ref="loadingScreen">

            <table class="table table-borderless">

                <thead>
                <tr class="header">
                    <th>
                        <a href="javascript:;" @click="sort('id')">
                            ID
                            <i class="fa"
                               :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                               v-if="currentSort == 'id'"
                            ></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" @click="sort('first_name')">
                            Name
                            <i class="fa"
                               :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                               v-if="currentSort == 'first_name'"
                            ></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" @click="sort('email')">
                            Email
                            <i class="fa"
                               :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                               v-if="currentSort == 'email'"
                            ></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" @click="sort('mobile_number')">
                            Phone Number
                            <i class="fa"
                               :class="{ 'fa-caret-up' : sortDirection, 'fa-caret-down' : !sortDirection }"
                               v-if="currentSort == 'mobile_number'"
                            ></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;">
                            Status
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;">
                            Is Blocked
                        </a>
                    </th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="( value , key ) in users.data" :key="key" class="pointer">
                    <td class="align-middle" @click="goToUser(value.id)">{{ value.id }}</td>
                    <td class="align-middle" @click="goToUser(value.id)">{{ value.first_name }} {{ value.last_name }}</td>
                    <td class="light-dark align-middle" @click="goToUser(value.id)">{{value.email}}</td>
                    <td class="light-dark align-middle" @click="goToUser(value.id)">{{value.mobile_number}}</td>
                    <td class="light-dark align-middle" @click="goToUser(value.id)">{{getStatus(value)}}</td>
                    <td class="light-dark align-middle" @click="goToUser(value.id)">{{ value.disabled_at != null ? 'Yes' : 'No' }}</td>
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
                                        @click="editUser( value.id )">
                                        Edit
                                    </a>

                                </li>

                                <li class="dropdownList">

                                    <a
                                        v-if="!!value.deleted_at"
                                        class="black"
                                        href="javascript:"
                                        @click="reactivateUser( value.id )">
                                        Reactivate
                                    </a>
                                    <a
                                        v-else
                                        class="black"
                                        href="javascript:"
                                        @click="openUserDeletionModal( value.id )">
                                        Delete
                                    </a>

                                </li>

                                <li v-if="value.deleted_at == null" class="dropdownList">

                                    <a
                                        v-if="!!value.disabled_at"
                                        class="black"
                                        href="javascript:"
                                        @click="enableUser( value.id )">
                                        Enable
                                    </a>
                                    <a
                                        v-else
                                        class="black"
                                        href="javascript:"
                                        @click="disableUser( value.id )">
                                        Disable
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
            :data="users"
            @pagination-change-page="getResults"
            :limit="5">
        </pagination>

    </div>

    <div id="deleteUserModal" class="modal fade">

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
                    <i
                        class="icon-warning icon-2x"
                        style="color: orange;" ></i>
                    Are you sure you want to delete user {{ user.full_name }}?
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="triggerDeleteUser">
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

    <div id="editUserModal" class="modal fade">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> {{ user.id ? 'Edit User' : 'Add User' }}</h4>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-hidden="true">
                        &times;
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form m-2">

                        <div class="form-group mt-2 mb-0">

                            <label
                                for="email"
                                class="m-2">
                                Username
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                readonly
                                v-model="user.email"/>
                        </div>
                        <div class="form-group mt-2 mb-0">

                            <label
                                for="first_name"
                                class="m-2">
                                First Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="first_name"
                                placeholder=""
                                v-model="user.first_name"/>

                        </div>
                        <div class="form-group mt-2 mb-0">

                            <label
                                for="last_name"
                                class="m-2">
                                Last Name
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="last_name"
                                placeholder=""
                                v-model="user.last_name"/>

                        </div>

                        <div class="form-group mt-2 mb-0">

                            <label
                                for="number"
                                class="m-2">
                                Mobile Number
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="number"
                                placeholder=""
                                v-model="user.mobile_number"/>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="saveUser">
                        Save
                    </button>

                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                        Close
                    </button>

                    <input
                        type="hidden"
                        name="id"
                        id="id"
                        value="user.id"/>

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
    name: 'user-list',
    components: {
        LoadingScreen,
        Pagination,
    },
    data() {
        return {
            token: '',
            users: {},
            user: {
                profile_photo_url : '/images/blank_face.jpg',
                full_name: '',
            },
            searchQuery : '',
            searchFilter: 'first_name',
            pageLimit: 10,
            currentSort: 'id',
            sortDirection: true, //true = ASC, false = DESC
            endpoints: {
                user: '/ajax/user',
                users: '/ajax/users',
            },
        }
    },
    methods: {
        async getResults( page ) {
            if (typeof page === 'undefined') {
                page = 1;
            }
            await axios.get( `${this.endpoints.users}?page=${page}`, {
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
                    if( response.success ) {
                        this.users = response.data.users;
                    } else {
                        this.$notify.error({
                            title:'Error',
                            message:response.message,
                            duration: 2000,
                        })
                    }
                }).catch(error => {
                    this.$notify.error({
                        title:'Error',
                        message:'Something went wrong',
                        duration: 2000,
                    })
                });
        },
        editUser( user_id ) {
            this.userSelected( user_id );
            $('#editUserModal').modal();
        },
        openUserDeletionModal( user_id ) {
            this.userSelected( user_id );
            $('#deleteUserModal').modal();
        },
        triggerDeleteUser(){
            this.$refs.loadingScreen.load(this.deleteUser());
        },
        async deleteUser() {
            await axios.delete( this.endpoints.user, {
                params : {
                    user_id : this.user.id
                }
            })
                .then(( result ) => {
                    let response = result.data;
                    if( response.success ) {
                        _.remove(this.users, {
                            id: this.user.id
                        });
                        this.$refs.loadingScreen.load(this.getResults());
                        $('#deleteUserModal').modal( 'toggle' );
                        this.$notify.success({
                            title:'Success',
                            message:'User deleted.',
                            duration: 2000,
                        })
                    }else {
                        this.$notify.error({
                            title:'Error',
                            message:response.message,
                            duration: 2000,
                        })
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title:'Error',
                        message:'Something went wrong',
                        duration: 2000,
                    });
                });
        },
        saveUser() {
            axios.post( this.endpoints.user, this.user )
                .then(( result ) => {
                    let response = result.data;
                    if( response.success ) {
                        this.user  =  response.data.user;
                        this.$notify.success({
                            title:'Success',
                            message:'Success',
                        });
                        $('#editUserModal').modal( 'toggle' );
                    }else {
                        this.$notify.error({
                            title:'Error',
                            message:response.message,
                        })
                    }
                })
                .catch(error => {
                    this.$notify.error({
                        title:'Error',
                        message:'Something went wrong',
                    })
                });
        },
        goToUser( user_id ) {
            location.href =`/admin/user/${user_id}`;
        },
        userSelected( user_id ) {
            this.user = _.find(this.users.data, {id: user_id});
        },
        openUserModal( user_id = null) {
            if( ! user_id ){
                this.$refs.loadingScreen.load(this.resetUser());
            }
            this.userSelected( user_id );
            $('#userModal').modal()
        },
        closeUserModal() {
            $('#userModal').modal( 'toggle' );
            this.$refs.loadingScreen.load(this.resetUser());
        },
        resetUser() {
            this.user = {
                profile_photo_url : '/images/blank_face.jpg',
                full_name: '',
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
        getStatus(item) {
            return !!item.deleted_at ? 'Deleted' : 'Active'
        },
        reactivateUser(userId) {
            this.$confirm('Are you sure you want to reactivate this User?', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios.post(this.endpoints.user.concat(`/${userId}/activate`))
                    .then(_ => {
                        this.$refs.loadingScreen.load(this.getResults());
                        this.$message({
                            type: 'success',
                            message: 'User reactivated!'
                        });
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: err && err.response.data.message ? err.response.data.message : 'Something went wrong'
                        })
                    })
            })
        },
        enableUser(userId) {
            this.$confirm('Are you sure you want to enable this User?', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios.post(this.endpoints.user.concat(`/${userId}/enable`))
                    .then(_ => {
                        this.$refs.loadingScreen.load(this.getResults());
                        this.$message({
                            type: 'success',
                            message: 'User enabled!'
                        });
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: err && err.response.data.message ? err.response.data.message : 'Something went wrong'
                        })
                    })
            })
        },
        disableUser(userId) {
            this.$confirm('Are you sure you want to disable this User?', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios.post(this.endpoints.user.concat(`/${userId}/disable`))
                    .then(_ => {
                        this.$refs.loadingScreen.load(this.getResults());
                        this.$message({
                            type: 'success',
                            message: 'User disabled!'
                        });
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: err && err.response.data.message ? err.response.data.message : 'Something went wrong'
                        })
                    })
            })
        },
    },
    mounted() {
        this.$refs.loadingScreen.load(this.getResults());
    }
}
</script>
