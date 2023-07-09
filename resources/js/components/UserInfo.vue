<template>
    <div id="user-info-content">
        <div class="page-header">

            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <a href="/admin/users">
                        Users
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    {{ user.full_name }}
                </li>
            </ol>

            <div class="action-buttons"></div>
        </div>

        <hr />

        <div class="content p-20">

            <el-card :body-style="{ padding: '0px' }" shadow="none">

                <div slot="header">
                    <hgroup>
                        <h2>{{user.full_name}}</h2>
                        <h6>{{user.email}}</h6>
                    </hgroup>
                </div>

                <!-- card body -->
                <div class="text item">

                    <loading-screen ref="loadingScreen">

                        <div class="container-fluid pt-3">
                            <div class="row">
                                <div class="col-2">
                                    <div class="mt-2">
                                        <img
                                            :src="user.profile_photo_url"
                                            class="img-fluid img-thumbnail"
                                            v-if="user.profile_photo_url"/>

                                        <img v-else src="/images/user-default.png" class="img-fluid img-thumbnail"/>
                                    </div>
                                </div>
                                <div class="col-10">

                                    <ul class="nav nav-tabs">

                                        <li class="nav-item ">
                                            <a
                                                class="nav-link"
                                                href="#details"
                                                data-toggle="tab"
                                                role="tab">
                                                Details
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a
                                                class="nav-link"
                                                href="#recommendations"
                                                data-toggle="tab"
                                                role="tab">
                                                Recommendations
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-4">
                                        <div
                                            id="details"
                                            class="tab-pane fade show active"
                                            role="tabpanel" >
                                            <el-form
                                                :model="user"
                                                :rules="rules"
                                                ref="user"
                                                label-width="180px">

                                                <el-form-item label="Photo">

                                                    <vue-dropzone
                                                        ref="myVueDropzone"
                                                        id="dropzone-single"
                                                        :options="dropzoneOptions"
                                                        v-on:vdropzone-sending="sendingEvent"
                                                        v-on:vdropzone-success="checkfields('user')"
                                                        class="col-lg-4">
                                                    </vue-dropzone>

                                                </el-form-item>

                                                <!-- <el-form-item label="Allowed to post" prop="is_allowed">

                                                    <el-switch
                                                        style="display: block"
                                                        v-model="user.is_allowed"
                                                        active-color="#13ce66"
                                                        inactive-color="#ff4949"
                                                        active-text="Allowed"
                                                        inactive-text="Blocked"
                                                        class="mt-2">
                                                    </el-switch>

                                                </el-form-item> -->

                                                <el-form-item label="Email" prop="email">

                                                    <el-input placeholder="Email" v-model="user.email"></el-input>

                                                </el-form-item>

                                                <el-form-item label="First Name" prop="first_name">

                                                    <el-input
                                                        v-model="user.first_name"
                                                        placeholder="First Name"
                                                        clearable>
                                                    </el-input>

                                                </el-form-item>

                                                <el-form-item label="Last Name" prop="last_name">

                                                    <el-input
                                                        v-model="user.last_name"
                                                        placeholder="Last Name"
                                                        clearable>
                                                    </el-input>

                                                </el-form-item>

                                                <el-form-item label="Mobile Number" prop="mobile_number">

                                                    <el-input
                                                        v-model="user.mobile_number"
                                                        placeholder="Mobile Number"
                                                        clearable>
                                                    </el-input>

                                                </el-form-item>

                                                <el-form-item label="Is Blocked" prop="is_allowed">

                                                    <el-switch
                                                        style="display: block"
                                                        v-model="user.is_blocked"
                                                        active-color="#13ce66"
                                                        inactive-color="#ff4949"
                                                        active-text="No"
                                                        inactive-text="Yes"
                                                        class="mt-2"
                                                        @change="toggleBlock"
                                                        >
                                                    </el-switch>

                                                </el-form-item>

                                                <el-form-item class="mt-2">

                                                    <el-button
                                                        type="primary"
                                                        @click="checkfields('user')">
                                                        Save
                                                    </el-button>

                                                </el-form-item>
                                                <!-- </form> -->
                                            </el-form>
                                        </div>
                                        <div id="recommendations" class="tab-pane fade"  role="tabpanel">
                                            <div class="row">
                                                <div class="col">
                                                    <h5>
                                                        Total Recommendations:
                                                        <strong>{{ recommendationsTotal }}</strong>
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <el-table
                                                        :data="recommendations"
                                                        stripe
                                                        border>
                                                        <el-table-column
                                                        label="ID"
                                                        width="100"
                                                        prop="id">
                                                        </el-table-column>
                                                        <el-table-column
                                                        label="Item Name"
                                                        prop="item_name"
                                                        width="400"
                                                        >
                                                        </el-table-column>
                                                        <el-table-column
                                                        label="Location"
                                                        prop="location"
                                                        width="300">
                                                        </el-table-column>
                                                        <el-table-column
                                                        label="Status"
                                                        prop="status">
                                                        </el-table-column>
                                                        <el-table-column
                                                        label="Recommended Price"
                                                        width="200">

                                                        <template slot-scope="scope">
                                                            {{ formatPrice(scope.row.recommended_price) }}
                                                        </template>

                                                        </el-table-column>
                                                        <el-table-column
                                                        label="Operations" align="center">
                                                        <template slot-scope="scope">
                                                            <el-button
                                                            size="mini"
                                                            type="danger"
                                                            :disabled="scope.row.status == 'Deactivated'"
                                                            @click="toggleStatus(scope.$index, scope.row)"
                                                            >
                                                            Deactivate
                                                            </el-button>
                                                        </template>
                                                        </el-table-column>
                                                    </el-table>
                                                    <div class="text-center">

                                                        <ul class="pagination">

                                                            <li class="page-item">
                                                                <button
                                                                    class="page-link"
                                                                    @click="getUserRecommendations(pagination.prev_page_url)"
                                                                    :disabled="!pagination.prev_page_url">
                                                                    Previous
                                                                </button>
                                                            </li>

                                                            <li>
                                                                <span class="p-2">
                                                                    Page {{pagination.current_page}} of {{pagination.last_page}}
                                                                </span>
                                                            </li>

                                                            <li class="page-item">
                                                                <button
                                                                    class="page-link"
                                                                    @click="getUserRecommendations(pagination.next_page_url)"
                                                                    :disabled="!pagination.next_page_url">
                                                                    Next
                                                                </button>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </loading-screen>

                </div>

            </el-card>
        </div>
    </div>
</template>
<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import LoadingScreen from 'vue-loading-screen';

export default {
    name: 'user-info',
    components: {
        vueDropzone: vue2Dropzone,
        LoadingScreen,
    },
    data() {
        return {
            uid: null,
            user: {
                full_name: '',
                first_name: '',
                last_name: '',
                mobile_number: '',
                is_allowed: null,
                is_blocked: null,
            },
            recommendationsTotal: 0,
            recommendations: [],
            pagination: {},
            currentSort: 'id',
            sortDirection: 'asc',
            endpoints: {
                user: '/ajax/user',
                users: '/ajax/users',
                userRecommendations: '/ajax/user/recommendations',
            },
            dropzoneOptions: {
                url: '/ajax/user/photo/upload',
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
            },
            imageUrl: '',
            rules: {
                first_name: [{
                    required: true,
                    message: 'First Name is required',
                    trigger: 'blur',
                },{
                    max: 150,
                    message: 'Length should be up to 150 characters only',
                    trigger: 'blur',
                }],
                last_name: [{
                    required: true,
                    message: 'Last Name is required',
                    trigger: 'blur',
                },{
                    max: 150,
                    message: 'Length should be up to 150 characters only',
                    trigger: 'blur',
                }],
                mobile_number: [{
                    max: 150,
                    message: 'Length should be up to 150 characters only.',
                    trigger: 'blur',
                }],
                email: [{
                    required: true,
                    message: 'Email is required',
                    trigger: 'blur',
                }, {
                    type: 'email',
                    message: 'Must be a valid email',
                    trigger: 'blur'
                }],
            }
        }
    },
    methods: {
        async getUser() {
            let result = await axios.get(this.endpoints.user, {
                params: { uid: this.uid }
            });
            let data = result.data.data;
            this.user = {
                ...data.user,
                is_blocked: !data.user.disabled_at
            };
            this.uid = this.user.id;
        },
        async getUserRecommendations(page_url) {
            page_url = page_url || this.endpoints.userRecommendations;
            await axios.get(page_url, {
                params: { userId: this.user.id }
            }).then(result => {
                const data = result.data
                const recommendations = data.data
                const meta = data.meta
                const links = data.links
                this.recommendations = recommendations.map(recommendation => ({
                    id: recommendation.id,
                    item_name: recommendation.item.item_name,
                    location: recommendation.location.location,
                    recommended_price: recommendation.recommended_price,
                    status: !recommendation.deleted_at ? 'Active' : 'Deactivated'
                }))
                this.recommendationsTotal = meta.total
                this.makePagination(meta, links)
            });
        },
        makePagination: function(meta, links){
            let pagination = {
                current_page: meta.current_page,
                last_page: meta.last_page,
                next_page_url: links.next,
                prev_page_url: links.prev,
            }
            this.pagination = pagination;
        },
        async saveUser() {
            try {
                let { disabled_at, ...rest } = this.user
                let result = await axios.post(this.endpoints.user, rest);

                let response = result.data;
                if (response.success) {
                    this.user = {
                        ...response.data.user,
                        is_blocked: !response.data.user.disabled_at
                    };
                    this.$notify({
                        title: 'Success',
                        message: 'User updated',
                        type: 'success',
                        duration: 2000,
                    });
                    this.removePhotos();
                    $('#editUserModal').modal('hide');
                } else {
                    this.$notify({
                        title: 'Error',
                        message: response.message,
                        duration: 2000,
                    });
                }
            } catch (error) {
                this.$notify.error({
                    title: 'Error',
                    message: 'Something went wrong',
                    duration: 2000,
                });
            }
        },
        checkfields(formName) {
            this.$refs[formName].validate(valid => {
                if (valid) {
                    this.$refs.loadingScreen.load(this.saveUser());
                } else {
                    return false;
                }
            });
        },
        removePhotos() {
            this.$refs.myVueDropzone.removeAllFiles();
        },
        sendingEvent(file, xhr, formData) {
            formData.append('uid', this.uid);
        },
        async toggleBlock() {
            let action = this.user.is_blocked ? 'enable' : 'disable'

            let result = await axios.post(`${this.endpoints.user}/${this.uid}/${action}`);

            if (result.data.success) {
                this.$notify({
                    title: 'Success',
                    message: `User has been ${action}d`,
                    type: 'success',
                    duration: 2000,
                });
            } else {
               this.$notify({
                    title: 'Error',
                    message: result.data.message,
                    type: 'error',
                    duration: 2000,
                }); 
            }
        },
        async toggleStatus(index, recommendation) {
            let action = 'deactivate'

            this.$confirm(`Are you sure you want to ${action} this recommendation? You cannot be able to reactivate this anymore.`, 'Warning', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios.post(`/ajax/user/${this.user.id}/recommendations/${recommendation.id}/${action}`)
                    .then(_ => {
                        this.$notify({
                            title: 'Success',
                            message: `Recommendation has been ${action}d`,
                            type: 'success',
                            duration: 2000,
                        });
                    }).finally(_ => {
                        this.getUserRecommendations()
                    })
            })
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2)
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }
    },
    async mounted() {
        this.uid = $('#uid').val();
        $('.users').addClass('active');
        await this.$refs.loadingScreen.load(this.getUser());
        await this.$refs.loadingScreen.load(this.getUserRecommendations());
    }
}
</script>

